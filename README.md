# jsnlib-cart
這是一個簡單的購物車，不依賴任何 framework。

### __construct($param = []) 
建構子
- sess (選)使用的 session 名稱，預設是 'jsnlib_cart'
````php 
$cart = new Jsnlib\Cart();
或
$cart = new Jsnlib\Cart(['sess' => 'mycart']);
````

````php
session_start();
require_once 'vendor/autoload.php';
$cart = new Jsnlib\Cart;
````

insert, update 都需要這些參數 
- primaryid (string) 唯一編號
- name (string) 產品名稱
- quantity (int) 產品數量
- price (int) 產品金額
- option (array) 夾帶參數

### insert(array $param): bool
將產品放入購物車
````php
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
$cart->insert($ary);
````

### update(array $param): bool
修改購物車的產品項目。如果5指定參數數量 quantity 為 0 時，視同刪除。

````php
$ary = 
[
    'primaryid' => 'JSN5000003',
    'quantity'  => 12
];
$cart->update($ary);
````

### isnew($primaryid): bool
加入購物車的是新商品？
````php
$cart->isnew('JSN5000003'); 
````

### delete($primaryid): bool
刪除購物車中的某個產品
````php
$cart->delete('JSN5000003');
````

### get($primaryid) 
取得已在購物車的產品資訊
````php
$cart->get('JSN5000000');
````

### truncate(): bool
清空購物車
````php
$cart->truncate();
````

### order(array $exclude = NULL): array
取得帳單
````php
$cart->order();
````
取得排除某些列表，例如排除運費的帳單
````php
$cart->order(['Transport']);
````

### total(array $exclude = NULL): int
合計
````php
$cart->total();
````
取得排除某些列表，例如排除運費的合計
````php
$cart->total(['Transport']);
````
