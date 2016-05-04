# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "debian/wheezy64"

  config.vm.network "private_network", ip: "192.168.33.10"
  config.vm.hostname = "my.briop.dev"

  unless Vagrant.has_plugin?("vagrant-hostsupdater")
    puts 'vagrant-hostsupdater is not installed!'
    puts 'To install the plugin, run:'
    puts 'vagrant plugin install vagrant-hostsupdater'
    exit
  end

  config.hostsupdater.aliases = ["www.my.briop.dev"]

  config.vm.synced_folder ".", "/vagrant", type: "virtualbox", owner: "www-data", group: "www-data"

  config.vm.provision :shell, path: "vagrant/bootstrap.sh"
end
