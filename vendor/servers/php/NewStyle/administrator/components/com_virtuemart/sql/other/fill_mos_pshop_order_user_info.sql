## If you have no data in the Table mos_pshop_oder_user_info after an update,
## run these queries

## Billto- Addresses
INSERT INTO mos_pshop_order_user_info
SELECT '', order_id, mos_pshop_orders.user_id, address_type,address_type_name, company, title, last_name,  first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, email
FROM mos_users, mos_pshop_orders
WHERE id = mos_pshop_orders.user_id;

## Ship-To Addresses
INSERT INTO mos_pshop_order_user_info
SELECT '', order_id, mos_pshop_orders.user_id, address_type,address_type_name, company, title, last_name,  first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, user_email
FROM mos_pshop_user_info, mos_pshop_orders
WHERE mos_pshop_orders.user_info_id = mos_pshop_user_info.user_info_id;
