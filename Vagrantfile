Vagrant.configure("2") do |config|
    config.vm.box = "debian/jessie64"
    config.vm.provider "virtualbox" do |v|
      v.memory = 4048
      v.cpus = 2
    end
    config.vm.network :private_network, ip: "33.33.33.10"
    config.vm.network :forwarded_port, guest: 80, host: 8080,  :name => "http", :auto => true
    config.vm.network :forwarded_port, guest: 15672, host: 15672,  :name => "rabbimqUI", :auto => true
    config.vm.synced_folder ".", "/vagrant", type: "nfs"
    config.vm.provision "shell", path: "provision/provision.sh"
    config.vm.provision "fix-no-tty", type: "shell" do |s|
        s.privileged = false
        s.inline = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
    end
    config.ssh.insert_key = false

    config.trigger.after [:provision, :up, :reload] do |trigger|
       trigger.run = {inline: 'echo "
                               rdr pass on lo0 inet proto tcp from any to self port 80 -> 127.0.0.1 port 8080
                               rdr pass on en0 inet proto tcp from any to any port 80 -> 127.0.0.1 port 8080
                               rdr pass on en1 inet proto tcp from any to any port 80 -> 127.0.0.1 port 80810
                                 " | sudo pfctl -ef - > /dev/null 2>&1;'}
       trigger.info = "==> Fowarding Ports: 80 -> 8080 & Enabling pf"
    end

  config.trigger.after [:halt, :destroy] do |trigger|
     trigger.run = {inline: "sudo pfctl -df /etc/pf.conf > /dev/null 2>&1;"}
     trigger.info = "==> Removing Port Forwarding & Disabling pf"
  end

  end