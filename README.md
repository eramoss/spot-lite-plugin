# Spot Lite
Um criador de relatórios de atividades com inteligencia e automação.

Feito em conjunto com o LITE (UNIVALI), Laboratório de inivação e tecnologia da educação.


## Instalação para desenvolvimento

### Requisitos
- Docker
- Docker Compose

### Instalação
1. Clone o repositório
```bash
git clone https://github.com/eramoss/spot-lite-plugin.git
```

2. Entre na pasta do projeto
```bash
cd spot-lite-plugin
```

3. Inicie o projeto
```bash
docker compose up -d --build
```
> Utilize o comando com -d para rodar em background. Os logs do projeto podem ser acessados em `http://localhost:3000`

### Acessos
- O wordpress com o plugin pré instalado pode ser acessado em `http://localhost:8888`.Basta seguir o passo a passo de instalação padrão do wordpress.
- O Banco de dados está desponivel com acesso no phpmyadmin em `http://localhost:8081`. Se prefirir, pode acessar o banco de dados diretamente com as credenciais descritas no arquivo `docker-compose.yml` nas variáveis de ambiente, eg. MYSQL_ROOT_PASSWORD, MYSQL_USER, MYSQL_PASSWORD.
- Os Logs do projeto estão disponíveis em `http://localhost:3000`. Se necessário utilize as credenciais padrão `admin` e `admin` para acessar o grafana.


## Estrutura do projeto
usado o padrão de desenvolvimento de plugins do wordpress [Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate), o projeto está dividido em 3 pastas principais:

```
/spot-lite-plugin
  /admin
    gerenciamento do que acontece no painel de administração pelo plugin
  /includes
    arquivos de inclusão de funções e classes
    projetos externos
  /public
    arquivos públicos do projeto e o que acontece no front-end
```

## Documentação
A documentação do projeto está disponível em `/docs`.
Cada parte do projeto está documentada em arquivos markdown, com instruções de uso e configuração.


## Contribuidores
- [Eduardo Ramos](https://github.com/eramoss)
- [Gabriel Schaldach](https://github.com/schaldach)
- [Jonatas Hellmann](https://github.com/JonasRH355)
- [Leonardo Cardoso](https://github.com/leonardodbc)
- [Diogo Viana](https://github.com/DigSix)
- [LITE - UNIVALI](https://lite.univali.br/)