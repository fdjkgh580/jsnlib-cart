# jsnlib-cart
這是一個簡單的購物車，不依賴任何 framework。

insert, update 都需要這些參數 
- primaryid (string) 唯一編號
- name (string) 產品名稱
- quantity (int) 產品數量
- price (int) 產品金額
- option (array) 夾帶參數

## insert($param): bool
將產品放入購物車

## update($param): bool
修改購物車的產品項目

## isnew($primaryid): bool
加入購物車的是新商品？

## delete($primaryid): bool
刪除購物車中的某個產品

## get($primaryid) 
取得已在購物車的產品資訊

## truncate(): bool
清空購物車

## order(array $exclude = NULL): array
取得帳單

## total(array $exclude = NULL): int
合計總額
