echo 'Fetch user...'
whoami
echo 'Curdir..'
ls
echo 'DIR?'
ls /home/ubuntu/engima
cd /home/ubuntu/engima
git stash
git pull origin deployCICD
echo 'Deleting screen...'
screen -X -S engima quit
echo 'Creating .env'
cp ENV.SAMPLE .env
echo 'Entering screen...'
screen -S engima
echo 'Run the container...'
sudo docker-compose up
echo 'Process done.'