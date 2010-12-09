rewrite ^/wap$ /wap/index.php last;
rewrite ^/index$ /index.php last;
rewrite ^/index/p.(.+)$ /index.php?page=$1 last;
rewrite ^/keywords/(.+)/p.(.+)$ /keywords.php?q=$1&p=$2 last;
rewrite ^/keywords/(.+)$ /keywords.php?q=$1 last;
rewrite ^/keywords$ /keywords.php last;
rewrite ^/search$ /search.php last;
rewrite ^/search/p.(.+)$ /search.php?page=$1 last;
rewrite ^/home/(.+)/(.+)/p.(.+)$ /home.php?hm=$2&user_name=$1&page=$3 last;
rewrite ^/home/(.+)/(.+)$ /home.php?hm=$2&user_name=$1 last;
rewrite ^/([0-9a-zA-Z]+)$ /home.php?user_name=$1 last;
rewrite ^/([0-9a-zA-Z]+)/profile$ /home.php?user_name=$1&profile=1 last;
rewrite ^/([0-9a-zA-Z]+)/profile/(.+)$ /home.php?user_name=$1&profile=1&hms=$2 last;
rewrite ^/op/feedback/p.(.+)$ /op.php?op=feedback&page=$1 last;
rewrite ^/op/web/(.+)$ /op.php?op=web&act=$1 last;
rewrite ^/op/follows/(.+)/p.(.+)$ /op.php?op=follows&user_name=$1&page=$2 last;
rewrite ^/op/follows/(.+)$ /op.php?op=follows&user_name=$1 last;
rewrite ^/op/privatemsg/my/p.([0-9]+)$ /op.php?op=privatemsg&pm=my&page=$1 last;
rewrite ^/op/privatemsg/send/p.([0-9]+)$ /op.php?op=privatemsg&pm=send&page=$1 last;
rewrite ^/op/privatemsg/my$ /op.php?op=privatemsg&pm=my last;
rewrite ^/op/privatemsg/send$ /op.php?op=privatemsg&pm=send last;
rewrite ^/op/view/(.+)$ /op.php?op=view&id=$1 last;
rewrite ^/op/(.+)$ /op.php?op=$1 last;
rewrite ^/op$ /op.php last;
rewrite ^/([0-9a-zA-Z]+).rss$ /api/rss.php?user_name=$1 last;
break;