FROM gitpod/workspace-full

RUN sudo apt-get -y install apt-transport-https ca-certificates gnupg lsb-release \
    && curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg \
    && echo \
      "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
      $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null \
    && sudo apt-get -y update \
    && sudo apt-get -y install docker-ce docker-ce-cli containerd.io \
    && sudo curl -fsSL -o /usr/local/bin/lando "https://files.lando.dev/cli/lando-linux-x64-edge" \
    && sudo chmod +x /usr/local/bin/lando \
    && mkdir -p ~/.lando/cache \
    && echo -e "proxy: 'OFF'\nbindAddress: '0.0.0.0'\nproxyBindAddress: '0.0.0.0'" > ~/.lando/config.yml \
    && lando --clear