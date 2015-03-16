# Azure Resource Service API

This project provides resources operations based on Azure SDK.

## Requirements

* PHP5.6+
* [PHP pthreads](https://github.com/krakjoe/pthreads)
* Mysql 5.5+

## Deployment Steps:

1.  Include Azure SDK
``` bash
    cd addons/
    git clone https://github.com/popfeng/azure_resource_service.git
```

2.  Start the deamon script
``` bash
    cd script/task
    /bin/php fire.php &
```
