<?php

namespace My_MVC\Database;

use Exception;
use My_MVC\File\File;
use My_MVC\Http\Request;
use My_MVC\Url\Url;
use PDO;

class Database
{
 protected static $instance;
 protected static $connection;

 protected static $select;
 protected static $table;
 protected static $join;
 protected static $where;
 protected static $where_binding = [];
 protected static $group_by;
 protected static $having;
 protected static $having_binding = [];
 protected static $order_by;
 protected static $limit;
 protected static $offset;
 protected static $query;
 protected static $binding = [];

 protected static $setter;
 private function __construct()
 {
 }


 private static function connect()
 {
  if (!static::$connection) {

   $database_data = File::require_file('config/Database.php');
   extract($database_data);
   $dsn = 'mysql:dbname=' . $database . ';host=' . $host . '';
   $option = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::ATTR_PERSISTENT => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES " . $charset . " COLLATE " . $collation,

   ];
   // die(phpinfo());
   try {
    static::$connection = new PDO($dsn, $username, $password, $option);
   } catch (\PDOException $e) {
    throw new Exception($e->getMessage());
   }
  }
 }



 private static function instance()
 {
  static::connect();

  if (!self::$instance) {
   self::$instance = new Database();
  }
  return self::$instance;
 }

 public  static function query($query = null)
 {
  static::instance();
  // die($query)
  if ($query == null) {
   if (!static::$table) {
    throw new Exception('Unkown table');
   }
   // select * from user join roles.id  = user.role_id where id > ? limit 1 offset 2
   $query = "SELECT ";
   $query .= static::$select ?: ' * ';
   $query .= " FROM " . static::$table . " ";
   $query .= static::$join . " ";
   $query .= static::$where . " ";
   $query .= static::$group_by . " ";
   $query .= static::$having . " ";
   $query .= static::$order_by . " ";
   $query .= static::$limit . " ";
   $query .= static::$offset . " ";
  }


  static::$query = $query;
  static::$binding = array_merge(static::$where_binding, static::$having_binding);
  return static::instance();
 }


 public  static function select()
 {
  $select = func_get_args();
  $select = implode(', ', $select);

  static::$select = $select;
  return static::instance();
 }

 public  static function table($table)
 {
  static::$table = $table;
  return static::instance();
 }

 public  static function join($table, $first, $operator, $second, $type = "INER")
 {
  static::$join .= " " . $type . " JOIN " . $table . " ON " . $first . $operator . $second . ' ';
  return static::instance();
 }

 public  static function rightJoin($table, $first, $operator, $second)
 {
  static::join($table, $first, $operator, $second, 'RIGHT');
  return static::instance();
 }


 public  static function leftJoin($table, $first, $operator, $second)
 {
  static::join($table, $first, $operator, $second, 'LEFT ');
  return static::instance();
 }


 public  static function where($column, $operator, $value, $type = null)
 {
  $where = "`" . $column . "` " . $operator . " ? ";

  if (!static::$where) {
   $statement = " WHERE " . $where;
  } else {

   if ($type == null) {
    $statement = " AND " . $where;
   } else {
    $statement = " " . $type . " " . $where;
   }
  }

  static::$where .= $statement;
  static::$where_binding[] = htmlspecialchars($value);
  return static::instance();
 }


 public  static function orWhere($column, $operator, $value)
 {
  static::where($column, $operator, $value, $type = "OR");
  return static::instance();
 }

 public static function groupBy()
 {
  $group_by = func_get_args();
  $group_by = " GROUP BY " . implode(', ', $group_by) . " ";

  static::$group_by = $group_by;
  return static::instance();
 }




 public  static function having($column, $operator, $value)
 {
  $having = "`" . $column . "` " . $operator . " ? ";

  if (!static::$having) {
   $statement = " HAVING " . $having;
  } else {
   $statement = " AND " . $having;
  }

  static::$having .= $statement;
  static::$having_binding[] = htmlspecialchars($value);
  return static::instance();
 }

 public static function orderBy($column, $type = null)
 {

  $sep = static::$order_by ? " , " : " ORDER BY ";
  $type = strtoupper($type);

  $type = ($type != null && in_array($type, ['ASC', 'DESC'])) ? $type : "ASC";
  $statement = $sep . $column . ' ' . $type . ' ';

  static::$order_by .= $statement;
  return static::instance();
 }




 public  static function limit($limit)
 {
  static::$limit =  " LIMIT " . $limit . ' ';
  return static::instance();
 }


 public  static function offset($offset)
 {
  static::$offset =  " OFFSET " . $offset . ' ';
  return static::instance();
 }

 public static function fetchExecute()
 {
  static::query(static::$query);
  $query = trim(static::$query, ' ');
  $data  = static::$connection->prepare($query);
  $data->execute(static::$binding);
  static::clear();
  return $data;
 }


 public static function get()
 {
  $data = static::fetchExecute();
  $result = $data->fetchAll();

  return $result;
 }

 // first record of result
 public static function first()
 {
  $data = static::fetchExecute();
  $result = $data->fetch();

  return $result;
 }

 private static function execute(array $data = [], $query, $where = null)
 {

  static::instance();

  if (!static::$table) {
   throw new Exception("Unknown table");
  }


  foreach ($data as $key => $value) {
   static::$setter .= "`" . $key . "` = ?, ";
   static::$binding[] = filter_var($value, FILTER_SANITIZE_STRING);
  }


  static::$setter = trim(static::$setter, ', ');
  $query .= static::$setter;
  $query .= ($where != null) ? static::$where . ' ' : '';

  static::$binding = ($where != null) ? array_merge(static::$binding, static::$where_binding) : static::$binding;
  $data = static::$connection->prepare($query);
  $data->execute(static::$binding);

  static::clear();
 }


 public  static function insert($data)
 {
  $table = static::$table;
  $query = "INSERT INTO " . $table . " SET ";
  static::execute($data, $query);

  $object_id = static::$connection->lastInsertId();
  $object = static::table($table)->where('id', '=', $object_id)->first();
  return $object;
 }



 public  static function update($data)
 {

  $query = "UPDATE " . static::$table . " SET ";
  static::execute($data, $query, true);
  return true;
  // $object_id = static::$connection->lastInsertId();
  // $object = static::table($table)->where('id','=',$object_id)->first();
  // return $object;
 }


 public  static function delete()
 {

  $query = "DELETE  FROM " . static::$table . " ";
  static::execute([], $query, true);

  return true;
 }



 public static function paginate($items_per_page = 15)
 {
  static::query(static::$query);
  $query = trim(static::$query, ' ');

  $data = static::$connection->prepare($query);
  $data->execute();

  $pages = ceil($data->rowCount() / $items_per_page);
  $page = Request::get('page');

  $current_page = (!is_numeric($page) || Request::get('page') < 1) ? '1' : $page;
  $offset = ($current_page - 1) * $items_per_page;

  static::limit($items_per_page);
  static::offset($offset);
  static::query();

  $data = static::fetchExecute();
  $result = $data->fetchAll();


  $response = ['data' => $result, 'items_per_page' => $items_per_page, 'pages' => $pages, 'current_page' => $current_page];

  return $response;
 }


 public  static function links($current_page, $total_pages)
 {
  // $links = '';


  // $from = $current_page - 2;
  // $to = $current_page + 2;
  // if($from < 2){
  //  $from = 2;
  //  $to = $from + 4;
  // }

  // if($to >= $pages){
  //  $diff = $to - $pages + 1;
  //  $from  = ($from > 2) ? ($from - $diff) :2;
  //  $to = $pages - 1;
  // }


  $pre_2  = $current_page - 2 >= 1 ? $current_page - 2 : 0;
  $pre_1  = $current_page - 1 >= 1 ? $current_page - 1 : 0;

  $current_page   = $current_page;

  $next_1 = $current_page + 1 <= $total_pages ? $current_page + 1 : 0;
  $next_2 = $current_page + 2 <= $total_pages ? $current_page + 2 : 0;

  $pre    = $pre_1;//previous
  $next   = $next_1;//next




  $page_url = Url::path(Request::url()).'?page=';

  $link_pre = $pre != 0 ? '<li><a class="frist"    href="' . $page_url   . $pre . '">Previous</a></li>' : "";
  $link_pre_2 = $pre_2 != 0 ? '<li><a   href="' . $page_url   . $pre_2 . '">' . $pre_2 . '</a></li>' : "";
  $link_pre_1 =  $pre_1 != 0 ? '<li><a   href="' . $page_url   . $pre_1 . '">' . $pre_1 . '</a></li>' : '';


  $link_current_page =  '<li><a class="active"    href="' . $page_url   . $current_page . '">' . $current_page . '</a></li>';


  $link_next_1 =  $next_1 != 0 ? '<li><a  href="' . $page_url   . $next_1 . '">' . $next_1 . '</a></li>' : "";
  $link_next_2 = $next_2 != 0 ? '<li><a  href="' . $page_url   . $next_2 . '">' . $next_2 . '</a></li>' : '';
  $link_next  = $next != 0 ? '<li><a  class="last"  href="' . $page_url   . $next . '">Next</a></li>' : '';



  $links = '<div><ul>' . $link_pre . $link_pre_2 . $link_pre_1 . $link_current_page . $link_next_1 . $link_next_2 . $link_next . '</ul></div>';

  return $links;
 }



 private  static function clear()
 {
  static $select = '';
  static $table = '';
  static $join = '';
  static $where = '';
  static $where_binding = [];
  static $group_by = '';
  static $having = '';
  static $having_binding = [];
  static $order_by = '';
  static $limit = '';
  static $offset = '';
  static $query = '';
  static $binding = [];
  static $setter = '';
 }


 public  static function getQuery()
 {
  static::query(static::$query);
  return static::$query;
 }
}
