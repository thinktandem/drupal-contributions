FROM mcr.microsoft.com/vscode/devcontainers/universal:1-linux

USER root

RUN apt-get remove docker docker-engine docker.io containerd runc \
    && apt-get update && apt-get install ca-certificates curl gnupg lsb-release \
    && curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
    $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null \
    && apt-get update && apt-get install docker-ce docker-ce-cli containerd.io -y

USER codespace