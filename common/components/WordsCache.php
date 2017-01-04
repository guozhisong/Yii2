<?php
namespace common\components;

use yii\base\Component;
use yii\caching\Cache;
use Yii;

class WordsCache extends Component
{
    /**
     * @var Cache|string the cache object or the application component ID of the cache object.
     * Settings will be cached through this cache object, if it is available.
     *
     * After the Settings object is created, if you want to change this property,
     * you should only assign it with a cache object.
     * Set this property to null if you do not want to cache the settings.
     */
    public $cache = 'cache';

    /**
     * To be used by the cache component.
     *
     * @var string cache key
     */
    public $cacheKey = 'expand';

    /**
     * Holds a cached copy of the data for the current request
     *
     * @var mixed
     */
    private $_data = null;

    private $_hotData = null;

    private $_parentidsData = null;

    private $_treeArr = null;

    private $_tree = null;

    private $_list = null;

    private $_kv = null;

    private $_hotTree = null;

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (is_string($this->cache)) {
            $this->cache = Yii::$app->get($this->cache, false);
        }
        $this->cache->cachePath = Yii::getAlias('@app/../common/cache');
    }

    public function setKV()
    {
        $data = $this->getKVData();
        $this->clearCache();
        $this->cache->set($this->cacheKey.'KV', $data);
    }

    /**
     * 所有的键值对
     **/
    public function getKV()
    {
        if ($this->_kv === null) {
            if ($this->cache instanceof Cache) {
                $data = $this->cache->get($this->cacheKey.'KV');
                if ($data === false) {
                    $data = $this->getKVData();
                    $this->cache->set($this->cacheKey.'KV', $data);
                }
            } else {
                $data = $this->getKVData();
            }
            $this->_kv = $data;
        }
        return $this->_kv;
    }

    public function getKVData() {
        $_data = Yii::$app->db->createCommand("SELECT name, id FROM {{%".$this->cacheKey."}} ORDER BY id ASC")->queryAll();
        return \yii\helpers\ArrayHelper::map($_data, 'id', 'name');
    }

    /*
     * Clears the settings cache on demand.
     * If you haven't configured cache this does nothing.
     *
     * @return boolean True if the cache key was deleted and false otherwise
     */
    public function clearCache()
    {
        $this->_data = null;
        if ($this->cache instanceof Cache) {
            $this->cache->cachePath = Yii::getAlias('@app/../common/cache');

            if($this->cache->get($this->cacheKey.'KV')) $this->cache->delete($this->cacheKey.'KV');
        }
    }
//
//    public function getList()
//    {
//        if ($this->_list === null) {
//            if ($this->cache instanceof Cache) {
//                $data = $this->cache->get($this->cacheKey.'List');
//                if ($data === false) {
//                    $data = $this->getListData();
//                    $this->cache->set($this->cacheKey.'List', $data);
//                }
//            } else {
//                $data = $this->getListData();
//            }
//            $this->_list = $data;
//        }
//        return $this->_list;
//    }
//
//    public function getListData() {
//        $_data = Yii::$app->db->createCommand("SELECT * FROM {{%".$this->cacheKey."}} ORDER BY listorder,areaid ASC")->queryAll();
//        $data = [];
//        if ($_data) {
//            foreach ($_data as $val) {
//                $data[$val['areaid']] = $val;
//            }
//        }
//        return $data;
//    }
//    /**
//     * 热门地区
//     * **/
//    public function getHotArea() {
//
//        if ($this->_hotData === null) {
//            if ($this->cache instanceof Cache) {
//                $data = $this->cache->get($this->cacheKey.'Hot');
//                if ($data === false) {
//                    $data = $this->getHotAreaData();
//                    $this->cache->set($this->cacheKey.'Hot', $data);
//                }
//            } else {
//                $data = $this->getHotAreaData();
//
//            }
//            $this->_hotData = $data;
//        }
//        return $this->_hotData;
//    }
//
//    public function getHotAreaData() {
//        $_data = Yii::$app->db->createCommand("SELECT areaname,areaid FROM {{%".$this->cacheKey."}} WHERE ishot = 1 AND isopen = 1 ORDER BY listorder,areaid ASC")->queryAll();
//        return \yii\helpers\ArrayHelper::map($_data, 'areaid', 'areaname');
//    }
//
//    /**
//     * 获取省份的地区以及省份下边的城市，组成数组
//     * **/
//    public function getTreeArr()
//    {
//        if ($this->_treeArr === null) {
//            if ($this->cache instanceof Cache) {
//                $data = $this->cache->get($this->cacheKey.'TreeArr');
//                if ($data === false) {
//                    $data = $this->getTreeArrData();
//                    $this->cache->set($this->cacheKey.'TreeArr', $data);
//                    $this->cache->set($this->cacheKey.'HotTreeArr', $this -> _hotTree);
//                }
//            } else {
//                $data = $this->getTreeArrData();
//            }
//            $this->_treeArr = $data;
//        }
//        return $this->_treeArr;
//    }
//    /**
//     * 获取省份的地区以及省份下边的热门城市，组成数组
//     * **/
//    public function getHotTreeArr()
//    {
//        if ($this->_hotTree === null) {
//            if ($this->cache instanceof Cache) {
//                $this->getTreeArrData();
//                $this->cache->set($this->cacheKey.'HotTreeArr', $this -> _hotTree);
//            }
//        }else{
//            $data = $this->cache->get($this->cacheKey . 'HotTreeArr');
//        }
//        return $this->_hotTree = $data;
//    }
//
//    /**
//     * Returns the data array
//     *
//     * @return array
//     */
//    public function getTreeArrData()
//    {   //只查询省，直辖市，地级市，北京市下的区不查询出来
//        //$_data = Yii::$app->db->createCommand("SELECT areaname,parentid,areaid,pinyin,hypy FROM {{%".$this->cacheKey."}} WHERE (zhixiashi = 0 OR zhixiashi is null) AND isopen = 1 ORDER BY listorder,areaid ASC")->queryAll();
//        $_data = Yii::$app->db->createCommand("SELECT areaname,ishot,parentid,areaid,pinyin,hypy FROM {{%".$this->cacheKey."}} WHERE  isopen = 1 and parentid !=1 ORDER BY listorder,areaid ASC")->queryAll();
//        $_list = $_pids = $data = $hotTree = [];
//        if ($_data) {
//            foreach ($_data as $val) {
//                if ($val['parentid'] > 0) {
//                    $_list[$val['parentid']][] = $val;
//                }else{
//                    $_pids[] = $val;
//                }
//                if($val['ishot'] == 1){
//                    $hotTree[] = $val;
//                }
//            }
//            $_data = [];
//            foreach ($_pids as $k => $rows) {
//                $rows['child'] = $this->getArray($_list, $rows['areaid']);
//                //$rows['level'] = 0;
//                $data[$rows['areaid']] = $rows;
//
//            }
//        }
//        $this -> _hotTree = $hotTree;
//        return $data;
//    }
//
//    /**
//     * 获取开启地区以及该地区下边的城市，组成数组
//     * **/
//    public function getTree()
//    {
//        if ($this->_tree === null) {
//            if ($this->cache instanceof Cache) {
//                $data = $this->cache->get($this->cacheKey.'Tree');
//                if ($data === false) {
//                    $data = $this->getTreeData();
//                    $this->cache->set($this->cacheKey.'Tree', $data);
//                }
//            } else {
//                $data = $this->getTreeData();
//            }
//            $this->_tree = $data;
//        }
//        return $this->_tree;
//    }
//
//    public function getTreeData()
//    {
//        $_data = Yii::$app->db->createCommand("SELECT areaname,parentid,areaid,zhixiashi,pinyin,hypy FROM {{%".$this->cacheKey."}} WHERE isopen = 1 ORDER BY listorder,areaid ASC")->queryAll();
//        $_list = $_pids = $data = [];
//        if ($_data) {
//            foreach ($_data as $val) {
//                if ($val['parentid'] > 0) {
//                    $_list[$val['parentid']][] = $val;
//                }else{
//                    $_pids[] = $val;
//                }
//            }
//            $_data = [];
//            foreach ($_pids as $k => $rows) {
//                $rows['child'] = $this->getArray($_list, $rows['areaid']);
//                //$rows['level'] = 0;
//                $data[$rows['areaid']] = $rows;
//
//            }
//        }
//        return $data;
//    }
//
//    public function getArray($data, $id, $level = 0){
//        $level++;
//        $arr = [];
//        $result = isset($data[$id]) ? $data[$id] : [];
//        if($result && is_array($result)) {
//            foreach ($result as $rows) {
//                $rows['child'] = $this->getArray($data, $rows['areaid'], $level);
//                //$rows['level'] = $level;
//                $arr[$rows['areaid']] = $rows;
//            }
//            return $arr;
//        }
//        return $arr;
//    }
//    /*
//     * 获取一线城市
//     */
//    /**
//     * 获取省份的地区以及省份下边的城市，组成数组
//     * **/
//    public function getFirstTier()
//    {
//        if ($this->_treeArr === null) {
//            if ($this->cache instanceof Cache) {
//                $data = $this->cache->get($this->cacheKey.'FirstTier');
//                if ($data === false) {
//                    $data = $this->getFirstTierData();
//                    $this->cache->set($this->cacheKey.'FirstTier', $data);
//                }
//            } else {
//                $data = $this->getFirstTierData();
//            }
//            $this->_treeArr = $data;
//        }
//        return $this->_treeArr;
//    }
//
//    /**
//     * Returns the data array
//     *
//     * @return array
//     */
//    public function getFirstTierData()
//    {
//        $_data = Yii::$app->db->createCommand("SELECT areaname,parentid,areaid,pinyin,hypy FROM {{%".$this->cacheKey."}} WHERE parentid=0 AND isopen = 1 ORDER BY listorder,areaid ASC")->queryAll();
//        $_list = $_pids = $data = [];
//        if ($_data) {
//            foreach ($_data as $val) {
//
//                $data[$val['areaid']] = $val;
//
//            }
//            $_data = [];
//
//        }
//        return $data;
//    }

//    public function createKVCache() {
//        $data = $this->getKVData();
//        $this->cache->set($this->cacheKey.'KV', $data);
//    }
//
//    public function createListCache() {
//        $data = $this->getListData();
//        $this->cache->set($this->cacheKey.'List', $data);
//    }
//
//    public function createTreeCache() {
//        $data = $this->getTreeData();
//        $this->cache->set($this->cacheKey.'Tree', $data);
//    }
//
//    public function createTreeArrCache() {
//        $data = $this->getTreeArrData();
//        $this->cache->set($this->cacheKey.'TreeArr', $data);
//        //生成热门地区Tree数组
//        $this->cache->set($this->cacheKey.'HotTreeArr', $this -> _hotTree);
//    }
//
//    public function createHotAreaCache() {
//        $data = $this->getHotAreaData();
//        $this->cache->set($this->cacheKey.'Hot', $data);
//    }
//
//    public function createAreaCache() {
//        $this->clearCache();
//        $this->createKVCache();
//        $this->createListCache();
//        $this->createTreeCache();
//        $this->createTreeArrCache();
//        $this->createHotAreaCache();
//    }

}
