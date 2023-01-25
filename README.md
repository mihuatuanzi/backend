# mihuatuanzi
米花团子后端项目

## 创建应用密钥
```shell
mkdir -p var/secret/ && cd var/secret/
# 创建密钥
openssl genrsa -out app_rsa.private.pem 4096
openssl rsa -in app_rsa.private.pem -pubout -out app_rsa.public.pem
# 创建 docker secret
docker secret create mihuatuanzi_app_rsa_private ./app_rsa.private.pem
docker secret create mihuatuanzi_app_rsa_public ./app_rsa.public.pem
# 接入阿里云密钥
printf "<secret-value>" | docker secret create mihuatuanzi_app_ali_access_key_secret -
```

## 初始化 Backend
_首先部署 [Ingress](https://github.com/mihuatuanzi/ingress) 服务_
```shell
# 将 Backend 部署到 mihuatuanzi swarm 中
docker stack deploy -c stack.yml -c stack.dev.yml mihuatuanzi
```

## 相关文档
### Doctrine
https://symfony.com/doc/current/doctrine.html

https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/basic-mapping.html

https://symfony.com/doc/current/validation.html
