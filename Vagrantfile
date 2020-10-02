# -*- mode: ruby -*-
# vi: set ft=ruby :
require 'yaml'
current_dir = File.dirname(File.expand_path(__FILE__))
kwai_config_name = current_dir + "/kwai.development.yaml"

if File.exist? kwai_config_name then
    kwai_config = YAML.load_file(kwai_config_name)
else
    abort "No kwai.development.yaml file found"
end

Vagrant.configure("2") do |config|
  config.vm.box = "hashicorp/bionic64"
  config.vm.define "KWAI_API"

  config.vm.hostname = "api.kwai.com"

  config.vm.network "private_network", ip: "10.11.12.14"

  if Vagrant.has_plugin?('vagrant-hostmanager')
    config.hostmanager.enabled = true
    config.hostmanager.manage_host = true
  end

  config.vm.provision :shell, keep_color: true, path: "Vagrant.provision.sh",
    :args => [ kwai_config['database']['name'], kwai_config['database']['user'], kwai_config['database']['password'] ]
end
