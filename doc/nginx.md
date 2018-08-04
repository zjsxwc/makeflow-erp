conf file
```text
#for sf4.2
server {
    add_header X-Frame-Options "SAMEORIGIN";

    server_name makeflow.test;
    root /var/www/symfony4/makeflow/public;
    
    sendfile off;
    
    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }


    location ~ ^/index\.php(/|$) {
        fastcgi_pass php71-upstream;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }


    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    error_log /var/www/symfony4/makeflow/var/log/nginx_project_error.log;
    access_log /var/www/symfony4/makeflow/var/log/nginx_project_access.log;

}
```