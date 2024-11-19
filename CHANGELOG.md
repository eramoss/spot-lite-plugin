# Changelog
Visão geral de todas as mudanças nesse projeto.

## [Unreleased]

### Adicionado
Estrutura inicial do projeto 

/admin
  gerenciamento do que acontece no painel de administração pelo plugin

/includes
  arquivos de inclusão de funções e classes
  projetos externos

/public
  arquivos públicos do projeto e o que acontece no front-end

### Adicionado
- Arquivo de configuração do projeto (docker compose)
- documentação do projeto /docs para o projeto e README.md para instruções de instalação

### Modificado
- configuração de logs do projeto com grafana e loki

### Modificado 
- configuração de monitoramento (comentada para ativar) usando grafana e prometheus.

### Modificado
- melhoria na implementação da estrutura do banco de dados

### Removido
- indexes do banco de dados não usados ainda

### Adicionado
- Documentação da estrutura do banco de dados