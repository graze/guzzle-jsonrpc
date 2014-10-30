Vagrant.configure("2") do |config|

  # Box
  config.vm.box = "trusty64"
  config.vm.box_url = "https://vagrantcloud.com/ubuntu/boxes/trusty64/versions/1/providers/virtualbox.box"

  # Shared folders
  config.vm.synced_folder ".", "/srv"

  # Setup
  config.vm.provision :shell, :inline => "hostname guzzle-jsonrpc && locale-gen en_GB.UTF.8"
  config.vm.provision :shell, :inline => "apt-get update --fix-missing"
  config.vm.provision :shell, :inline => "apt-get install -q -y g++ make git curl"
  config.vm.provision :shell, :inline => "add-apt-repository ppa:chris-lea/node.js"
  config.vm.provision :shell, :inline => "add-apt-repository ppa:ondrej/php5-5.6 && apt-get update"
  config.vm.provision :shell, :inline => "apt-get install -q -y php5-cli php5-curl php5-xdebug nodejs"
  config.vm.provision :shell, :inline => "curl -s https://getcomposer.org/installer | php"
  config.vm.provision :shell, :inline => "mv ./composer.phar /usr/local/bin/composer"

end
