echo 'Fetch user...'
whoami
echo 'Curdir..'
ls
echo 'DIR?'
ls /home/ubuntu/engima
cd /home/ubuntu/engima
git stash
git pull origin master
echo 'Removing existing container...'
sudo docker-compose stop engima_php && sleep 10
echo 'Run the container...'
sudo docker-compose up -d
echo 'Process done.'