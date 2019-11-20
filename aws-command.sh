echo 'Fetch user...'
whoami
echo 'Curdir..'
ls
echo 'DIR?'
ls /home/ubuntu/engima
cd /home/ubuntu/engima
git stash
git checkout deployCICD
git pull origin deployCICD
echo 'Deleting screen...'
screen -X -S engima quit
echo 'Creating .env'
cp ENV.SAMPLE .env
echo 'Create executable...'
sudo chmod +x ./start-server.sh
echo 'Entering screen...'
screen -S engima ./start-server.sh