<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-30 13:52
 *
 */


namespace Nickfan\AppBox\Service\Drivers;


use Nickfan\AppBox\Service\BoxBaseRouteServiceDriver;
use Nickfan\AppBox\Service\BoxRouteServiceDriverInterface;

class DbBoxRouteServiceDriver extends BoxBaseRouteServiceDriver implements BoxRouteServiceDriverInterface {

    //protected static $driverKey = 'db';
    const DBTYPE_MSSQL = 'mssql';
    const DBTYPE_MYSQL = 'mysql';

    public function getDbTypeByVendorInstance($vendorInstance=null,$default=null){
        if(!is_null($vendorInstance)){
            if($vendorInstance instanceof \ezSQL_mssql){
                return self::DBTYPE_MSSQL;
            }elseif($vendorInstance instanceof \ezSQL_mysql || $vendorInstance instanceof \ezSQL_mysqli){
                return self::DBTYPE_MYSQL;
            }else{
                return $default;
            }
        }
        return $default;
    }
    /**
     * 初始化当前序列id
     */
    public function initSeq($objectName = '',$option=array(),$vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'objectLabel'=>'sequence',
            'init'=>0,
            'step'=>1,
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $sql = 'INSERT INTO '.$option['objectLabel'].' VALUES('.$vendorInstance->escape($objectName).','.intval($option['init']).','.intval($option['step']).')';
        return $vendorInstance->get_var($sql);
    }

    /**
     * 生成序列id
     */
    public function nextSeq($objectName = '',$option=array(),$vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'objectLabel'=>'sequence',
            'init'=>0,
            'step'=>1,
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $sql = 'SELECT nextval('.$vendorInstance->escape($objectName).')';
        return $vendorInstance->get_var($sql);
    }

    /**
     * 获取当前序列id
     */
    public function currentSeq($objectName = '',$option=array(),$vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'objectLabel'=>'sequence',
            'init'=>0,
            'step'=>1,
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $sql = 'SELECT currval('.$vendorInstance->escape($objectName).')';
        return $vendorInstance->get_var($sql);
    }

    /**
     * 设定当前序列id
     */
    public function setSeq($objectName = '',$val=0,$option=array(),$vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'objectLabel'=>'sequence',
            'init'=>0,
            'step'=>1,
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $sql = 'SELECT setval('.$vendorInstance->escape($objectName).','.intval($val).')';
        return $vendorInstance->get_var($sql);
    }


    /**
     * 查询数据项
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryVar($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        !array_key_exists('queryField', $queryStruct) && $queryStruct['queryField'] = array('id');
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        $queryStruct['limitOffset'] = array('limit'=>1);
        $sql = $this->compileSqlSelect($queryStruct,$vendorInstance);
        $result = $vendorInstance->get_var($sql);
        return $result;
    }

    /**
     * 查询数据行
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryRow($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        !array_key_exists('queryField', $queryStruct) && $queryStruct['queryField'] = array('*');
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        $queryStruct['limitOffset'] = array('limit'=>1);
        $sql = $this->compileSqlSelect($queryStruct,$vendorInstance);
        $result = $vendorInstance->get_row($sql,ARRAY_A);
        return $result;
    }

    /**
     * 查询数据列表
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryAssoc($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        !array_key_exists('queryField', $queryStruct) && $queryStruct['queryField'] = array('*');
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        $sql = $this->compileSqlSelect($queryStruct,$vendorInstance);
        $result = $vendorInstance->get_results($sql,ARRAY_A);
        return $result;
    }


    /**
     * 查询数据列表行数
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryCount($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        $queryStruct['queryField'] = array('COUNT(*) AS resultCount');
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        if(array_key_exists('orderSet', $queryStruct)){ unset($queryStruct['orderSet']); }
        if(array_key_exists('limitOffset', $queryStruct)){ unset($queryStruct['limitOffset']); }
        $sql = $this->compileSqlSelect($queryStruct,$vendorInstance);
        $result = $vendorInstance->get_var($sql);
        return $result;
    }


    /**
     * 查询数据结构（列表和总行数）
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryStruct($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        return array(
            'assoc'=>$this->queryAssoc($queryStruct,$option,$vendorInstance),
            'count'=>$this->queryCount($queryStruct,$option,$vendorInstance),
        );
    }

    /**
     * 执行插入
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryInsert($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        $sql = $this->compileSqlInsert($queryStruct,$vendorInstance);
        $result = $vendorInstance->query($sql);
        return $result;
    }

    /**
     * 执行替换
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryReplace($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        $sql = $this->compileSqlReplace($queryStruct,$vendorInstance);
        $result = $vendorInstance->query($sql);
        return $result;
    }

    /**
     * 执行更新
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryUpdate($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        $sql = $this->compileSqlUpdate($queryStruct,$vendorInstance);
        $result = $vendorInstance->query($sql);
        return $result;
    }


    /**
     * 执行删除
     * @param array $queryStruct
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryDelete($queryStruct=array(),$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $queryStruct
        );
        !array_key_exists('querySchema', $queryStruct) && $queryStruct['querySchema'] = array($this->getRouteKey());
        $sql = $this->compileSqlDelete($queryStruct,$vendorInstance);
        $result = $vendorInstance->query($sql);
        return $result;
    }

    /**
     * 执行sql
     * @param $sql
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function querySQL($sql,$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$sql)
        );
        return $vendorInstance->query($sql);
    }

    /**
     * 获取数据项 sql
     * @param $sql
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryVarSQL($sql,$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$sql)
        );
        return $vendorInstance->get_var($sql);
    }


    /**
     * 获取数据行 sql
     * @param $sql
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryRowSQL($sql,$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$sql)
        );
        return $vendorInstance->get_row($sql,ARRAY_A);
    }


    /**
     * 获取数据列表 sql
     * @param $sql
     * @param array $option
     * @param null $vendorInstance
     * @return mixed
     */
    public function queryAssocSQL($sql,$option=array(),$vendorInstance = null){
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$sql)
        );
        return $vendorInstance->get_results($sql,ARRAY_A);
    }

    /**
     * Determines if the string has an arithmetic operator in it.
     *
     * @param   string   string to check
     * @return  boolean
     */
    public static function hasOperator($string){
        return (bool) preg_match('/[<>!=]|\sIS(?:\s+NOT\s+)?\b|BETWEEN/i', trim($string));
    }

    public static function dbEscape($value='',$type=self::DBTYPE_MYSQL){
        switch (gettype($value))
        {
            case 'array':
            case 'object':
                if($type==self::DBTYPE_MSSQL){
                    $value = '\''.str_ireplace("'", "''", self::dataPack($value)).'\'' ;
                }elseif($type==self::DBTYPE_MYSQL){
                    $value = '\''.mysql_real_escape_string(stripslashes(self::dataPack($value))).'\'' ;
                }
                break;
            case 'string':
                if($type==self::DBTYPE_MSSQL){
                    $value = '\''.str_ireplace("'", "''", $value).'\'' ;
                }elseif($type==self::DBTYPE_MYSQL){
                    $value = '\''.mysql_real_escape_string(stripslashes($value)).'\'' ;
                }
                break;
            case 'boolean':
                $value = (int) $value;
                break;
            case 'double':
                // Convert to non-locale aware float to prevent possible commas
                $value = sprintf('%F', $value);
                break;
            default:
                $value = ($value === NULL) ? 'NULL' : $value;
                break;
        }
        return (string) $value;
    }

    public static function strEscape($value,$type=self::DBTYPE_MYSQL){
        if($type==self::DBTYPE_MSSQL){
            $value = str_ireplace("'", "''", $value);
        }elseif($type==self::DBTYPE_MYSQL){
            $value = mysql_real_escape_string(stripslashes($value));
        }
        return (string) $value;
    }

    /**
     * Escapes any input value.
     *
     * @param   mixed   value to escape
     * @return  string
     */
    public function escapeValue($value,$vendorInstance = null)
    {
        $dbType = $this->getDbTypeByVendorInstance($vendorInstance,self::DBTYPE_MYSQL);
        switch (gettype($value))
        {
            case 'string':
                $value = '\''.$vendorInstance->escape($value).'\'';
                break;
            case 'boolean':
                $value = (int) $value;
                break;
            case 'double':
                // Convert to non-locale aware float to prevent possible commas
                $value = sprintf('%F', $value);
                break;
            default:
                $value = ($value === null) ? 'NULL' : $value;
                break;
        }
        return (string) $value;
    }

    public function compileSqlCondition($queryStruct=array(),$vendorInstance = null){
        $dbType = $this->getDbTypeByVendorInstance($vendorInstance,self::DBTYPE_MYSQL);
        //* 处理输入条件
        $quote = isset($queryStruct['quote'])?(bool)$queryStruct['quote']:TRUE;
        // 查询的关键字条件
        $sqlConditionKey = '';
        if(isset($queryStruct['conditionKey']) && !empty($queryStruct['conditionKey'])){
            foreach($queryStruct['conditionKey'] as $key=>$value){
                if ($value === NULL)
                {
                    if ( ! self::hasOperator($key))
                    {
                        $key .= ' IS';
                    }
                    $value = ' NULL';
                }
                elseif (is_bool($value))
                {
                    if ( ! self::hasOperator($key))
                    {
                        $key .= ' =';
                    }
                    $value = ($value == TRUE) ? ' 1' : ' 0';
                }
                else
                {
                    if ( ! self::hasOperator($key) AND ! empty($key))
                    {
                        $key = $key.' =';
                    }
                    else
                    {
                        preg_match('/^(.+?)([<>!=]+|\bIS(?:\s+NULL))\s*$/i', $key, $matches);
                        if (isset($matches[1]) AND isset($matches[2]))
                        {
                            $key = trim($matches[1]).' '.trim($matches[2]);
                        }
                    }
                    $value = ' '.(($quote == TRUE) ? $this->escapeValue($value,$vendorInstance) : $value);
                }
                $sqlConditionKey .= ' AND '. $key . $value;
            }
            $sqlConditionKey = substr($sqlConditionKey,5);//strlen(' AND ') = 5
        }

        // 查询的in条件
        $sqlConditionIn = '';
        if(isset($queryStruct['conditionIn']) && !empty($queryStruct['conditionIn'])){
            foreach ($queryStruct['conditionIn'] as $key=>$value){
                if (is_array($value))
                {
                    $escapedValue = array();
                    foreach ($value as $v)
                    {
                        if (is_numeric($v))
                        {
                            $escapedValue[] = $v;
                        }
                        else
                        {
                            $escapedValue[] = "'".$vendorInstance->escape($v)."'";
                        }
                    }
                    $value = implode(",", $escapedValue);
                }
                $sqlConditionIn .= ' AND '.$key.' IN ( '.$value.')';
            }
            $sqlConditionIn = substr($sqlConditionIn,5);//strlen(' AND ') = 5
        }

        // 查询的like条件
        $sqlConditionLike = '';
        if(isset($queryStruct['conditionLike']) && !empty($queryStruct['conditionLike'])){
            foreach ($queryStruct['conditionLike'] as $key=>$value){
                $value = $vendorInstance->escape($value);
                $value = '%'.str_replace('%', '\\%', $value).'%';
                $sqlConditionLike .= ' AND '.$key.' LIKE \''.$value . '\'';
            }
            $sqlConditionLike = substr($sqlConditionLike,5);//strlen(' AND ') = 5
        }

        // 查询条件（where）整合
        $sqlCondition = '';
        //如果有任意一个条件非空
        if(!empty($sqlConditionKey)
            || !empty($sqlConditionIn)
            || !empty($sqlConditionLike)
        ){
            $sqlCondition .= ' WHERE ';
            $sqlConditionRow = array();
            !empty($sqlConditionKey) && $sqlConditionRow[]=$sqlConditionKey;
            !empty($sqlConditionIn) && $sqlConditionRow[]=$sqlConditionIn;
            !empty($sqlConditionLike) && $sqlConditionRow[]=$sqlConditionLike;
            $sqlCondition .= ' '.implode(' AND ',$sqlConditionRow);
        }
        return $sqlCondition;
    }

    public function compileSqlInsert($queryStruct=array(),$vendorInstance = null){
        $dbType = $this->getDbTypeByVendorInstance($vendorInstance,self::DBTYPE_MYSQL);
        //* 处理输入条件
        $quote = isset($queryStruct['quote'])?(bool)$queryStruct['quote']:TRUE;
        //最终的查询语句。
        $sqlString = 'INSERT INTO ';
        // 查询的库表
        $sqlSchema = '';
        if(isset($queryStruct['querySchema']) && !empty($queryStruct['querySchema'])){
            $sqlSchema .= implode(',',$queryStruct['querySchema']).' SET ';
        }
        // 查询的域
        $sqlField = '';
        if(isset($queryStruct['queryField']) && !empty($queryStruct['queryField'])){
            $sqlField = implode(',',$queryStruct['queryField']);
        }
        $sqlString .= $sqlSchema.$sqlField;
        return $sqlString;
    }
    public function compileSqlUpdate($queryStruct=array(),$vendorInstance = null){
        $dbType = $this->getDbTypeByVendorInstance($vendorInstance,self::DBTYPE_MYSQL);
        //* 处理输入条件
        $quote = isset($queryStruct['quote'])?(bool)$queryStruct['quote']:TRUE;
        //最终的查询语句。
        $sqlString = 'UPDATE ';
        // 查询的库表
        $sqlSchema = '';
        if(isset($queryStruct['querySchema']) && !empty($queryStruct['querySchema'])){
            $sqlSchema .= implode(',',$queryStruct['querySchema']).' SET ';
        }
        // 查询的域
        $sqlField = '';
        if(isset($queryStruct['queryField']) && !empty($queryStruct['queryField'])){
            $sqlField = implode(',',$queryStruct['queryField']);
        }
        $sqlCondition = $this->compileSqlCondition($queryStruct,$vendorInstance);
        // Limit分页条件
        $sqlLimitOffset = '';
        if($dbType==self::DBTYPE_MYSQL){
            if(isset($queryStruct['limitOffset']) && !empty($queryStruct['limitOffset']) && isset($queryStruct['limitOffset']['limit'])){
                $sqlLimitOffset .= ' LIMIT '.$queryStruct['limitOffset']['limit'];
            }
        }
        $sqlString .= $sqlSchema.$sqlField.$sqlCondition.$sqlLimitOffset;
        return $sqlString;
    }

    public function compileSqlReplace($queryStruct=array(),$vendorInstance = null){
        $dbType = $this->getDbTypeByVendorInstance($vendorInstance,self::DBTYPE_MYSQL);
        //* 处理输入条件
        $quote = isset($queryStruct['quote'])?(bool)$queryStruct['quote']:TRUE;
        //最终的查询语句。
        $sqlString = 'REPLACE INTO ';
        // 查询的库表
        $sqlSchema = '';
        if(isset($queryStruct['querySchema']) && !empty($queryStruct['querySchema'])){
            $sqlSchema .= implode(',',$queryStruct['querySchema']).' SET ';
        }
        // 查询的域
        $sqlField = '';
        if(isset($queryStruct['queryField']) && !empty($queryStruct['queryField'])){
            $sqlField = implode(',',$queryStruct['queryField']);
        }
        $sqlCondition = $this->compileSqlCondition($queryStruct,$vendorInstance);
        // Limit分页条件
        $sqlLimitOffset = '';
        if($dbType==self::DBTYPE_MYSQL){
            if(isset($queryStruct['limitOffset']) && !empty($queryStruct['limitOffset']) && isset($queryStruct['limitOffset']['limit'])){
                $sqlLimitOffset .= ' LIMIT '.$queryStruct['limitOffset']['limit'];
            }
        }
        $sqlString .= $sqlSchema.$sqlField.$sqlCondition.$sqlLimitOffset;
        return $sqlString;
    }

    public function compileSqlDelete($queryStruct=array(),$vendorInstance = null){
        $dbType = $this->getDbTypeByVendorInstance($vendorInstance,self::DBTYPE_MYSQL);
        //* 处理输入条件
        $quote = isset($queryStruct['quote'])?(bool)$queryStruct['quote']:TRUE;
        //最终的查询语句。
        $sqlString = 'DELETE ';
        // 查询的库表
        $sqlSchema = '';
        if(isset($queryStruct['querySchema']) && !empty($queryStruct['querySchema'])){
            $sqlSchema .= ' FROM '.implode(',',$queryStruct['querySchema']);
        }
        $sqlCondition = $this->compileSqlCondition($queryStruct,$vendorInstance);
        // Limit分页条件
        $sqlLimitOffset = '';
        if($dbType==self::DBTYPE_MYSQL){
            if(isset($queryStruct['limitOffset']) && !empty($queryStruct['limitOffset']) && isset($queryStruct['limitOffset']['limit'])){
                $sqlLimitOffset .= ' LIMIT '.$queryStruct['limitOffset']['limit'];
            }
        }
        $sqlString .= $sqlSchema.$sqlCondition.$sqlLimitOffset;
        return $sqlString;
    }

    /**
     * 编译sql select 语句
     * @param array $queryStruct
     * $queryStruct=array(
     *      'quote'=>TRUE,
     *      'queryField'=>array('id', ),
     *      'querySchema'=>array('Temp', ),
     *      'conditionKey'=>array(
     *          'id >'=>0,
     *          'key'=>'tmpval',
     *      ),
     *      'conditionIn'=>array(
     *          'id'=>array(1,3,5,7,8,9),
     *      ),
     *      'conditionLike'=>array(
     *          'key'=>'tmpstr',
     *      ),
     *      'orderSet'=>array(
     *          array('createTimestamp'=>'DESC'),
     *          array('id'=>'ASC'),
     *      ),
     *      'limitOffset'=>array(
     *          'offset'=>0,
     *          'limit'=>5,
     *      ),
     * );
     */
    public function compileSqlSelect($queryStruct=array(),$vendorInstance = null){
        $dbType = $this->getDbTypeByVendorInstance($vendorInstance,self::DBTYPE_MYSQL);
        //* 处理输入条件
        $quote = isset($queryStruct['quote'])?(bool)$queryStruct['quote']:TRUE;

        //最终的查询语句。
        $sqlString = 'SELECT ';
        //FIXME 目前只做了对业务日常需求所需要用到的key的对应构建，有空应该补齐对应的完整逻辑。

        // 查询的域
        $sqlField = '';
        if(isset($queryStruct['queryField']) && !empty($queryStruct['queryField'])){
            $sqlField = implode(',',$queryStruct['queryField']);
        }

        // 查询的库表
        $sqlSchema = '';
        if(isset($queryStruct['querySchema']) && !empty($queryStruct['querySchema'])){
            $sqlSchema .= ' FROM '.implode(',',$queryStruct['querySchema']);
        }


        $sqlCondition = $this->compileSqlCondition($queryStruct);

        // 排序条件
        $sqlOrder = '';
        if(isset($queryStruct['orderSet']) && !empty($queryStruct['orderSet'])){
            $sqlSortArray = array();
            foreach ($queryStruct['orderSet'] as $row=>$set){
                foreach($set as $field=>$direction){
                    $direction = strtoupper(trim($direction));
                    ! in_array($direction, array('ASC', 'DESC', 'RAND()', 'RANDOM()', 'NULL')) && $direction = 'ASC';
                    $sqlSortArray[] = $field.' '.$direction;
                }
            }
            $sqlOrder = ' ORDER BY '.implode(', ', $sqlSortArray);
        }

        // Limit分页条件
        $sqlLimitOffset = '';
        if($dbType==self::DBTYPE_MYSQL){
            if(isset($queryStruct['limitOffset']) && !empty($queryStruct['limitOffset'])){
                if(isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                    $sqlLimitOffset .= ' LIMIT '.$queryStruct['limitOffset']['offset'].','.$queryStruct['limitOffset']['limit'];
                }elseif (!isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                    $sqlLimitOffset .= ' LIMIT '.$queryStruct['limitOffset']['limit'];
                }
            }
            $sqlString .= $sqlField.$sqlSchema.$sqlCondition.$sqlOrder.$sqlLimitOffset;
        }elseif($dbType==self::DBTYPE_MSSQL){
            if(isset($queryStruct['limitOffset']) && !empty($queryStruct['limitOffset'])){
                if(isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                    $sqlLimitOffset .= ' TOP '.$queryStruct['limitOffset']['limit'];
                    $sqlString = 'SELECT ROW_NUMBER() OVER ('.$sqlOrder.') AS RowNumber ,'.$sqlField.$sqlSchema.$sqlCondition;
                    $sqlString = 'SELECT '.$sqlLimitOffset.' * FROM ('.$sqlString.') A WHERE RowNumber>'.$queryStruct['limitOffset']['offset'];
                }elseif (!isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                    $sqlLimitOffset .= ' TOP '.$queryStruct['limitOffset']['limit'].' ';
                    $sqlString .= $sqlLimitOffset.$sqlField.$sqlSchema.$sqlCondition.$sqlOrder;
                }
            }
            else
            {
                $sqlString .= $sqlField.$sqlSchema.$sqlCondition.$sqlOrder.$sqlLimitOffset;
            }
        }else{
            $sqlString .= $sqlField.$sqlSchema.$sqlCondition.$sqlOrder.$sqlLimitOffset;
        }

        //print('<pre>'.var_export($sqlString,TRUE).'</pre>');
        return $sqlString;
    }
}