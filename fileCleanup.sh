#!/bin/bash

FILE_PATH_PREFIX="/var/www/html/atlas-coding-orig"
for FILE_PATH in `cat /var/www/html/atlas-coding-orig/files-to-clean.txt`
do
	file $FILE_PATH
	php -l $FILE_PATH
	#TMP_FILE_PATH="$FILE_PATH-tmp";
	#TMP_FILE_PATH_CLEAN="$FILE_PATH-tmp-2";
	#cp $FILE_PATH $TMP_FILE_PATH;
	#file $TMP_FILE_PATH;
	#sed -e '1,19d' < $TMP_FILE_PATH > $TMP_FILE_PATH_CLEAN;
	#cp $TMP_FILE_PATH_CLEAN $FILE_PATH;
	#rm $TMP_FILE_PATH;
	#rm $TMP_FILE_PATH_CLEAN;
done
