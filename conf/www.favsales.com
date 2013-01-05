
server {
    listen 80 ;
    server_name favsales.com ;
    rewrite ^(.*) http://www.favsales.com$1 permanent;
}


server {
	listen 80;

	error_page 503 @503 ;
    #return 503 ;

	server_name   www.favsales.com ;
	root /var/www/vhosts/www.favsales.com/htdocs  ;
	index index.php index.html ;

	server_name_in_redirect  off;

	#rewrite rule for assets timestamp versioning
    rewrite ^/(css|js)/(.*)\.t(\d+)\.(css|js)$  /$1/$2.$4 ;

	try_files $uri $uri/ /index.php?q=$uri&$args;
	

    location @503 {
        #avoid redirect to built-in 503.
        try_files /site/503.html =503;
    }

	# for vhost location php context we need to set 
	# Document root explicitly, otherwise we get
	# NO INPUT file specified error

    location ~* \.(js|css|png|jpg|jpeg|gif)$ {
        expires 30d ;
        break ;
    }		
	
	location ~ \.php$ {
	 	try_files $uri =404;
        fastcgi_read_timeout 600 ;
		fastcgi_pass 127.0.0.1:9100 ;
	}  

}

