# mihuatuanzi
米花团子后端项目

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
