<?php
namespace lessok;
/**
 * 数据库操作类
 */
class Db extends \PDO
{
	private $config;
	private $table;   //当前操作的数据表名字
	private $where = array();
	private $field = '*';
	public $sql;
	private $stmt; //PDOStatement实例

	/**
	 * 初始化一个数据库实例
	 */
	public function __construct($config)
	{
		$this->config = $config;
		parent::__construct($config['dsn'], $config['user'], $config['pass'], $config['options']);
	}

	/**
	 * 插入数据
	 * @param array $data
	 */
	public function insert($data = array())
	{
		$this->sql = sprintf('INSERT INTO %s SET %s', $this->table, implode(',', $this->joinKeyVal($data,true)));
		if (!$this->query($this->sql)) {
			return false;
		}
		return $this->lastInsertId();
	}


	/**
	 * 计算有多少条记录
	 */
	public function count($strCountSql='count(*)')
	{
		$this->sql = sprintf('SELECT %s FROM %s %s', $strCountSql, $this->table, $this->getWhere());
		$sth = $this->query($this->sql);
		if(!$sth){
			return 0;
		}
		$res = $sth->fetchColumn(0);
		return (int)$res;
	}

	/**
	 * @param $sql
	 * @param int $type
	 */
	public function row($sql,$type=\PDO::FETCH_ASSOC)
	{
		$this->sql = $sql;
		$sth = $this->query($this->sql);
		if (!$sth) {
			return false;
		}
		return $sth->fetch($type);
	}

	public function rows($sql, $start=0, $size=0, $type=\PDO::FETCH_ASSOC)
	{
		$this->sql = $sql;
		$sth = $this->query($this->sql);
		if (!$sth) {
			return array();
		}
		return $sth->fetchAll($type)?:[];
	}

	/**
	 * 更新数据
	 * @param array $data
	 * 返回受影响的行数/出错返回false
	 */
	public function update($data = array(),$returnCnt=0)
	{
		$this->sql = sprintf('UPDATE %s SET %s %s' , $this->table, implode(',' , $this->joinKeyVal($data)) , $this->getWhere());
		if($returnCnt==1){
			$stmt = $this->prepare($this->sql);
			$stmt->execute();
			return $stmt->rowCount();
		}
		else {
			return $this->exec($this->sql);
		}
	}

	/**
	 * 根据查询条件返回一条记录
	 * @return array|mixed
	 */
	public function find()
	{
		$this->sql = 'SELECT '.$this->field.' FROM '.$this->table . $this->getWhere();
		return $this->row($this->sql, \PDO::FETCH_ASSOC);
	}

	/**
	 * 删除数据
	 */
	public function delete()
	{
		$this->sql = 'DELETE FROM '.$this->table. $this->getWhere();
		$ret = $this->exec($this->sql);
		return $ret!==false;
	}

	/**
	 * 设置各种查询条件
	 * @param mixed $input
	 */
	public function where($input)
	{
		if (is_string($input)&&!empty($input)) {
			$this->where[] = $input;
		} else if (is_array($input)){
			foreach($input as $k=>$v){
				if(is_numeric($k)){
					$this->where[]=$v;
				} else {
					$this->where[] = sprintf('`%s`=%s',$k,$this->quote($v));
				}
			}
		}
		return $this;
	}
    //获取sql的查询条件
	public function getWhere()
	{
		return count($this->where) ? ' WHERE '.implode(' AND ',$this->where) : '';
	}

	public function field($field='*')
	{	
		$this->field = $field;
		return $this;
	}

	/**
	 * 设置数据表名称
	 * @param string $name
	 */
	public function table($name)
	{
		$this->table = $name;
		$this->field = "*";
        $this->where = [];
		return $this;
	}

	public function reset()
	{
		$this->sql = '';
		$this->field = '*';
		$this->where = array();
		return $this;
	}

	/**
	 * @param array $data
	 * 拼接key valure 数组为 = 连接的数据
	 * $noEmpty不使用空数据
	 */
	public function joinKeyVal($data,$noEmpty=false)
	{
		$arr = array();
		foreach ($data?:[] as $k=>$v) {
			if (!empty($k)) {
				if (strlen($v)==0&&$noEmpty==false){
					array_push($arr, sprintf('`%s`=NULL',$k));
				} else {
					array_push($arr, sprintf('`%s`=%s',$k,$this->quote($v)));
				}
			}
		}
		return $arr;
	}
}


