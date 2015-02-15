# How to export database structure?

``` bash
    mysqldump --opt --extended-insert -d ucw_cmdb azure_res_item azure_res_item_cs azure_res_item_sa azure_res_item_vmd azure_res_item_vmd_role azure_res_item_vmd_role_port azure_res_item_vn azure_res_item_vn_subnet azure_res_op azure_res_status azure_vm_image azure_vm_size -u root -p > azure.sql
```
