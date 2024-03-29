<?php
/**
 * Oracle database driver for DataTables libraries.
 *
 * Note that this software uses the oci_* methods in PHP and NOT the Oracle PDO
 * driver, which is poorly supported.
 *
 *  @author    SpryMedia
 *  @copyright 2014 SpryMedia ( http://sprymedia.co.uk )
 *  @license   http://editor.datatables.net/license DataTables Editor
 *  @link      http://editor.datatables.net
 */

namespace DataTables\Database;
if (!defined('DATATABLES')) exit();

use PDO;
use DataTables\Database\Query;
use DataTables\Database\DriverOracleResult;


/**
 * Oracle driver for DataTables Database Query class
 *  @internal
 */
class DriverOracleQuery extends Query {
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Private properties
	 */
	private $_stmt;

	private $_editor_pkey_value;


	protected $_identifier_limiter = null;

	protected $_field_quote = '"';


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Public methods
	 */

	static function connect( $user, $pass='', $host='', $port='', $db='', $dsn='' )
	{
		if ( is_array( $user ) ) {
			$opts = $user;
			$user = $opts['user'];
			$pass = $opts['pass'];
			$port = $opts['port'];
			$host = $opts['host'];
			$db   = $opts['db'];
			$dsn  = isset( $opts['dsn'] ) ? $opts['dsn'] : '';
		}

		if ( $port !== "" ) {
			$port = ":{$port}";
		}

		if ( ! is_callable( 'oci_connect' ) ) {
			echo json_encode( array( 
				"error" => "oci methods are not available in this PHP install to connect to Oracle"
			) );
			exit(0);
		}

		$conn = @oci_connect($user, $pass, $host.$port.'/'.$db, 'utf8');

		if ( ! $conn ) {
			// If we can't establish a DB connection then we return a DataTables
			// error.
			$e = oci_error();

			echo json_encode( array( 
				"error" => "An error occurred while connecting to the database ".
					"'{$db}'. The error reported by the server was: ".$e['message']
			) );
			exit(0);
		}

		// Use ISO date and time styles
		$stmt = oci_parse($conn,  "ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'" );
		$res = oci_execute( $stmt );

		$stmt = oci_parse($conn,  "ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'" );
		$res = oci_execute( $stmt );

		return $conn;
	}


	public static function transaction ( $conn )
	{
		// no op
	}

	public static function commit ( $conn )
	{
		oci_commit( $conn );
	}

	public static function rollback ( $conn )
	{
		oci_rollback( $conn );
	}


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Protected methods
	 */

	protected function _prepare( $sql )
	{

		//echo $sql.'-------------';

		//print_r($this->_bindings);


		$resource = $this->database()->resource();
		$pkey = $this->pkey();

		// If insert, add the pkey column
		if ( $this->_type === 'insert' && $pkey ) {
			$sql .= ' RETURNING '.(is_array($pkey) ? $pkey[0] : $pkey).' INTO :editor_pkey_value';
		}

		$this->database()->debugInfo( $sql, $this->_bindings );

		$this->_stmt = oci_parse( $resource, $sql );

		// If insert, add a binding for the returned id
		if ( $this->_type === 'insert' && $pkey ) {
			oci_bind_by_name(
				$this->_stmt,
				':editor_pkey_value',
				$this->_editor_pkey_value,
				36
			);
		}

		// Bind values
		for ( $i=0 ; $i<count($this->_bindings) ; $i++ ) {
			$binding = $this->_bindings[$i];

			oci_bind_by_name(
				$this->_stmt,
				$binding['name'],
				$binding['value']
			);
		}
	}


	protected function _exec()
	{
		$res = @oci_execute( $this->_stmt, OCI_NO_AUTO_COMMIT );

		if ( ! $res ) {
			$e = oci_error( $this->_stmt );
			throw new \Exception( "Oracle SQL error: ".$e['message'] );

			return false;
		}

		$resource = $this->database()->resource();
		return new DriverOracleResult( $resource, $this->_stmt, $this->_editor_pkey_value );
	}


	protected function _build_table()
	{
		$out = array();

		for ( $i=0, $ien=count($this->_table) ; $i<$ien ; $i++ ) {
			$t = $this->_table[$i];

			if ( strpos($t, ' as ') ) {
				$a = explode( ' as ', $t );
				$out[] = $a[0].' '.$a[1];
			}
			else {
				$out[] = $t;
			}
		}

		return ' '.implode(', ', $out).' ';
	}


	// Oracle 12c+ only
	protected function _build_limit()
	{
		$out = '';

		if ( $this->_offset ) {
			$out .= ' OFFSET '.$this->_offset.' ROWS';
		}
		
		if ( $this->_limit ) {
			if ( ! $this->_offset ) {
				$out .= ' OFFSET 0 ROWS';
			}
			$out .= ' FETCH NEXT '.$this->_limit. ' ROWS ONLY';
		}

		return $out;
	}
}
