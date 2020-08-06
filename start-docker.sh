#!/bin/bash

if [ ! -f ./api/.docker.env ]; then
    echo "Copying ./api/.docker.env from ./api/.docker.env.example"
    cp ./api/.docker.env.example ./api/.docker.env
    sed -i -e "s/PUT_YOUR_ID/${UID}/g" ./api/.docker.env
    cat ./api/.docker.env | grep DEV_UID
fi

docker-compose up -d --build
