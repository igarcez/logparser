LogParser para Chaordic
==============

Desenvolvido por Ian Garcez <ian@onespace.com.br>

Iniciando os servidores
----------

Instale Vagrant e execute os comandos:

```
cd Servers
vagrant box add precise32 http://files.vagrantup.com/precise32.box
vagrant up
```

Instalando dependencias do aplicativo
---------

```
cd ..
composer install
```

Definindo configurações
---------

Na pasta config está os arquivos de configuração do aplicativo

**server.json**
Podem ser informados quantos servidores quanto necessário
`key_file` pode ter um caminho absoluto ou relativo a base do aplicativo
