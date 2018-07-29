host=10.88.1.32
user=callcenter
pass=callcenter
query=$(cat $1)

mysql -D $host -u $user -p $pass -se "$query"