echo 'Fetch user...'
whoami
echo 'Curdir..'
ls
echo 'DIR?'
ls /home/ubuntu/engima
cd /home/ubuntu/engima
git stash
git pull origin deployCICD
echo 'Removing existing container...'
sudo docker-compose stop engima_php && sleep 10
echo 'Creating .env'
cp ENV.SAMPLE .env
echo 'Run the container...'
sudo docker-compose up -d
echo 'Process done.'