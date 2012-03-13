if [ -n $1 ]; then
	folder="src/"
else
	folder=$1
fi

if [ -d $folder ]; then
	phpunit --stop-on-failure --bootstrap "bootstrap.php" $folder
else
	echo "File or folder $folder does not exist"
	exit
fi

