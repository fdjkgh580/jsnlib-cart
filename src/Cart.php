<?php
namespace Jsnlib;

class Cart {
    
    // session名稱
    public $sess;

    public function __construct(array $param = [])
    {
        session_start();

        $param += 
        [
            'sess' => 'jsnlib_cart'
        ];

        $this->sess =  $param['sess'];
        unset($_SESSION[$this->sess]);
        $_SESSION[$this->sess] = [];
    }

    /**
     * 參數是否為陣列
     * @param  array $param 指定參數
     */
    private function check_param_is_array($param): bool
    { 
        if (!is_array($param)) 
            throw new \Exception('參數須要是陣列型態');
        
        if (!empty($param['option']) and !is_array($param['option'])) 
            throw new \Exception("參數option須要是陣列型態");
        
        return true;
    }
    
    /**
     * 檢查必要參數
     * @param  array  $param 指定參數
     */
    private function required(array $param): object
    {
        $this->check_param_is_array($param);

        $required = 
        [
            'primaryid' =>  0,
            'name'      =>  0,
            'quantity'  =>  0,
            'price'     =>  0,
            'option'    =>  0
        ];
        
        // 有出現的鍵    
        foreach ($param as $key => $val) $required[$key] = 1;
        
        // 尋找値為 0 的鍵
        $res_key = array_search(0, $required);

        if ($res_key === 0) 
            throw new \Exception("請指定參數：{$res_key}");
        
        return $this;
    }
    
    /**
     * 加入購物車的是新商品？
     * @param   $primaryid  唯一編號
     */
    public function isnew($primaryid): bool
    {
        return empty($_SESSION[$this->sess][$primaryid]) ? true : false;
    }

    /**
     * 運行單項產品加總
     * @param   price 價格
     * @param   quantity 數量
     */
    private function single_count($param): bool
    {
        $key        = $param['primaryid'];
        $price      = $_SESSION[$this->sess][$key]['price'];
        $quantity   = $_SESSION[$this->sess][$key]['quantity'];
        
        $_SESSION[$this->sess][$key]['count'] = $price * $quantity;
        
        return true;
    }
        
    /**
     * 新增
     * @param array $param 產品參數
     * @return true 新增成功 | false 代表商品已存在
     */
    public function insert(array $param): bool
    {
        $this->required($param);
        $isnew = $this->isnew($param['primaryid']);
        if ($isnew == false) return false;
        
        $key                                     = $param['primaryid'];
        $_SESSION[$this->sess][$key]['name']     = $param['name'];
        $_SESSION[$this->sess][$key]['price']    = $param['price'];
        $_SESSION[$this->sess][$key]['quantity'] = $param['quantity'];
        $_SESSION[$this->sess][$key]['option']   = $param['option'];
        $this->single_count($param);
        
        return true;
    }
    
    /**
     * 修改購物車的項目
     * @param array $param 產品參數
     */
    public function update(array $param): bool
    {
        $isnew = $this->isnew($param['primaryid']);

        if (empty($param['primaryid'])) throw new \Exception('請指定修改的商品 primaryid');
        
        // 不存在這項商品
        if ($isnew == true) return false;
        
        $this->check_param_is_array($param);
        
        // 當修改 quantity 為 0 時將視同刪除
        if (isset($param['quantity']) and $param['quantity'] == "0")
        {
            $this->delete($param['primaryid']);
            return true;
        }
        
        $item = $param['primaryid'];

        foreach ($param as $key => $val) 
        {
            if ($key == 'primaryid') continue;
            $_SESSION[$this->sess][$item][$key] = $val;
            $this->single_count($param);
        }

        return true;
    }
    
    
    /**
     * 刪除
     * @param   $primaryid 產品唯一編號
     */
    public function delete($primaryid): bool
    {
        // 若商品本身不存在
        if (empty($_SESSION[$this->sess][$primaryid])) return false;
        
        unset($_SESSION[$this->sess][$primaryid]);

        return true;        
    }
    
    /**
     * 取得已在購物車的產品資訊
     * @param   $primaryid 產品唯一編號
     * @return  若不存在為 false | 返回該產品列表
     */
    public function get($primaryid) 
    {
        if (array_key_exists($primaryid, $_SESSION[$this->sess]) == false) return false;

        $res = $_SESSION[$this->sess][$primaryid];

        return empty($res) ? false : $res;
    }

    /**
     * 尋找
     * @param  array  $param 可以是任和屬性
     * @return mix           若有搜尋到就返回列表，若找不到返回 false
     */
    public function find($param = [])
    {
        $res = \__::where($_SESSION[$this->sess], $param);

        return count($res) > 0 ? $res : false;
    }

    // 清空購物車
    public function truncate(): bool
    {
        unset($_SESSION[$this->sess]);

        return empty($_SESSION[$this->sess]) ? true : false;
    }

    /**
     * 排除的項目
     * @param  array      $order   訂單
     * @param  array|null $exclude 排除的 primaryid    
     * @return 訂單列表
     */
    protected function exclude(array $order, array $exclude = NULL): array
    {
        $newary = [];
        if (!is_array($exclude)) return $order;

        foreach ($order as $key => $val)
        {
            if (in_array($key, $exclude)) continue;
            $newary[$key] = $val;
        }

        $order = $newary;
        return $order;
    }
    
    
    /**
     * 取得帳單
     * @param   $exclude 排除的項目
     */
    public function order(array $exclude = NULL): array
    {
        $order = is_array($_SESSION[$this->sess]) ?  $_SESSION[$this->sess] : [];

        $result = $this->exclude($order, $exclude);
        
        return $result;
    }


    /**
     * 合計總額
     * @param   $exclude 要排除的項目，將不列入計算
     */
    public function total(array $exclude = NULL): int
    {
        $order = $this->order($exclude);
        $total = 0;

        foreach ($order as $key => $item)
        {
            $total += $item['count'];
        }

        return (int) $total;
    }
}