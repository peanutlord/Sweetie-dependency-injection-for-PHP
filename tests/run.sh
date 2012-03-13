if [ -z $1 ]; then
	folder="src/"
else
	folder=$1
fi

if [ -f "$PWD/$folder" ] || [ -d "$PWD/$folder" ]; then
	phpunit --stop-on-failure --bootstrap "bootstrap.php" $folder
else
	echo "File or folder $PWD/$folder does not exist"
	exit
fi

