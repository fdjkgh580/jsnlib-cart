<?
/*
 * 同商品不同屬性時，primaryid也就不一樣了
 */
session_start();
require_once 'vendor/autoload.php';

unset($_SESSION);
$cart = new Jsnlib\Cart;


//新增A款衣服XL號
$ary = 
[
    'primaryid' => 'JSN5000000',
    'name'      => 'A款衣服',
    'price'     => 399,
    'quantity'  => 2,
    'option'    =>      
    [
        'productid' => 'A00001',
        'size'      => 'XL',
    ]
];
$res = $cart->insert($ary);
if ($res === false) die("商品primaryid:{$ary['primaryid']}已存在"); 
else echo "商品編號{$ary['primaryid']}加入成功<br>";

// 再次新增A款衣服XL號，故意造成重複的判斷
$ary = 
[
    'primaryid' => 'JSN5000000',
    'name'      => 'A款衣服',
    'price'     => 399,
    'quantity'  => 2,
    'option'    =>      
    [
        'productid' => 'A00001',
        'size'      => 'XL',
    ]
];

//判斷是否重複的重要寫法   
$isnew = $cart->isnew($ary['primaryid']); 
if ($isnew === true) 
{
    $res = $cart->insert($ary);
    if ($res === false) die("商品primaryid:{$ary['primaryid']}已存在"); 
    else echo "商品編號{$ary['primaryid']}加入成功<br>";
}
else 
{
    echo "這是故意的。本商品primaryid:{$ary['primaryid']}已存在, 所以要改用update()<br>";
}

//新增A款衣服S號
$ary = 
[
    'primaryid' => 'JSN5000001',
    'name'      => 'A款衣服',
    'price'     => 399,
    'quantity'  => 1,
    'option'    =>      
    [
        'productid' => 'A00001',
        'size'      => 'S',
    ]
];
$res = $cart->insert($ary);
if ($res === false) die("商品primaryid:{$ary['primaryid']}已存在"); 
else echo "商品編號{$ary['primaryid']}加入成功<br>";

//新增C款襪子
$ary = 
[
    'primaryid' =>      'JSN5000003',
    'name'      =>      'C款襪子',
    'price'     =>      19,
    'quantity'  =>      6,
    'option'    =>      []
];
$res = $cart->insert($ary);
if ($res === false) die("商品primaryid:{$ary['primaryid']}已存在"); 
else echo "商品編號{$ary['primaryid']}加入成功<br>";

//新增H款帽子
$ary = 
[
    'primaryid'     =>      'JSN5000007',
    'name'      =>      'H款帽子',
    'price'     =>      188,
    'quantity'  =>      1,
    'option'    =>      
    [
        'color'     =>  'red'
    ]
];
$res = $cart->insert($ary);
if ($res === false) die("商品primaryid:{$ary['primaryid']}已存在"); 
else echo "商品編號{$ary['primaryid']}加入成功<br>";

//修改C款襪子數量
$ary = 
[
    'primaryid' =>      'JSN5000003',
    'quantity'  =>      12
];
$res = $cart->update($ary);
if ($res === true) echo "修改商品編號{$ary['primaryid']} 成功<br>";
else "修改商品編號{$ary['primaryid']} 失敗<br>";

//修改A款衣服S號數量維0時，視同刪除
$ary = 
[
    'primaryid' => 'JSN5000001',
    'quantity'  => 0
];
$res = $cart->update($ary);
if ($res === true) echo "修改商品編號{$ary['primaryid']} 成功<br>";
else "修改商品編號{$ary['primaryid']} 失敗<br>";

//刪除H款帽子
$res = $cart->delete('JSN5000007');
if ($res === true) echo "刪除商品編號JSN5000007 成功<br>";
else "刪除商品編號JSN5000007 失敗<br>";

//取得已存在購物車的商品項目
$res = $cart->get('JSN5000000');
if (!empty($res)) 
{
    echo "取得已存在購物車的商品編號 JSN5000000：";
    print_r($res);
    echo "<br>";
}
else "商品編號JSN5000000不存在購物車<br>";

//新增運費
$ary = 
[
    'primaryid'     =>      'Transport',
    'name'          =>      '貨到付款',
    'price'         =>      120,
    'quantity'      =>      1,
    'option'        =>      []
];
$res = $cart->insert($ary);
if ($res === false) die("運費primaryid:{$ary['primaryid']}已存在"); 
else echo "運費{$ary['primaryid']}加入成功<br>";

//取得帳單陣列
$order = $cart->order();
echo "取得帳單:<br>";
print_r($order);
echo "<hr>";



//取得帳單陣列，但排除指定的鍵，如運費
$order_exclude = $cart->order(['Transport']);
echo "取得帳單，但排除運費<br>";
print_r($order_exclude);
echo "<hr>";

//總額
$total = $cart->total();
echo "合計 {$total} 元<br>";

//總額，排除指定的建，如運費
$total = $cart->total(['Transport']);
echo "不含運費的小計 {$total} 元<br>";

//總數量
echo "購買商品共" .  count($order) . "件<br>";

//清空購物車
$res = $cart->truncate();
if ($res === true) echo "購物車已清空！<br>";
else echo "錯誤，購物車未清空！";
?>

