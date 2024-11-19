# Documentação do Esquema de Banco de Dados

## Ideia geral
O Spot lite plugin é feito para automatizar e facilitar a criação de registros de eventos (aulas do lite is cool) dentro de projetos (lite is cool) e a criação de relatórios desses eventos. O plugin é feito para ser usado em conjunto com o WordPress, e por isso, utiliza a mesma base de dados.

O relatório é um PDF que vai ser gerado pelos textos armazenados no banco como descreve as tabelas abaixo.
Este pode ter:
- Título
- Descrição geral do evento
- Data do evento
- Autor
- Palavras-chave para busca
- Atividades realizadas
- Fotos

Um relatório pertence a um projeto.

Atividades é uma tabela que mapeia um participante para uma descrição de atividade realizada.

Fotos é uma tabela que mapeia um relatório para uma URL de uma foto.

---

## Tabelas e Estrutura

### Tabela: `projects`
Armazena informações sobre projetos.

| Coluna         | Tipo         | Restrição                   | Descrição                              |
|----------------|--------------|-----------------------------|----------------------------------------|
| `id`           | INT(11)      | NOT NULL, AUTO_INCREMENT    | Identificador único do projeto.        |
| `name`         | VARCHAR(255) | NOT NULL                    | Nome do projeto.                       |
| `description`  | TEXT         |                             | Descrição do projeto.                  |
| `start_date`   | DATE         |                             | Data de início do projeto.             |
| `end_date`     | DATE         |                             | Data de término do projeto.            |
| `status`       | VARCHAR(50)  |                             | Status atual do projeto.               |
| **Chave Primária** | `id`      |                             |                                        |

---

### Tabela: `participants`
Armazena informações sobre os participantes.

| Coluna         | Tipo         | Restrição                   | Descrição                              |
|----------------|--------------|-----------------------------|----------------------------------------|
| `id`           | INT(11)      | NOT NULL, AUTO_INCREMENT    | Identificador único do participante.   |
| `name`         | VARCHAR(255) | NOT NULL                    | Nome do participante.                  |
| `birth_date`   | DATE         |                             | Data de nascimento do participante.    |
| `school`       | VARCHAR(255) |                             | Escola ou instituição do participante. |
| **Chave Primária** | `id`      |                             |                                        |

---

### Tabela: `reports`
Armazena relatórios associados a projetos.

| Coluna                     | Tipo              | Restrição                   | Descrição                                          |
|----------------------------|-------------------|-----------------------------|--------------------------------------------------|
| `id`                       | INT(11)          | NOT NULL, AUTO_INCREMENT    | Identificador único do relatório.               |
| `project_id`               | INT(11)          | NOT NULL                    | Referência ao projeto relacionado.              |
| `title`                    | VARCHAR(255)     | NOT NULL                    | Título do relatório.                            |
| `general_event_description`| TEXT             |                             | Descrição geral do evento.                      |
| `event_date`               | DATE             |                             | Data do evento.                                 |
| `author`                   | BIGINT UNSIGNED  |                             | Identificador do autor (usuário do WordPress).  |
| `keywords_for_search`      | TEXT             |                             | Palavras-chave para busca.                     |
| **Chave Primária**         | `id`             |                             |                                                 |
| **Chave Estrangeira**      | `project_id`     | REFERENCES `projects(id)` ON DELETE CASCADE | |
| **Chave Estrangeira**      | `author`         | REFERENCES `wp_users(ID)` ON DELETE SET NULL | |

---

### Tabela: `activities`
Armazena informações sobre atividades realizadas em um relatório.

| Coluna             | Tipo         | Restrição                   | Descrição                                       |
|--------------------|--------------|-----------------------------|-----------------------------------------------|
| `id`               | INT(11)      | NOT NULL, AUTO_INCREMENT    | Identificador único da atividade.             |
| `report_id`        | INT(11)      | NOT NULL                    | Referência ao relatório relacionado.          |
| `participant_id`   | INT(11)      | NOT NULL                    | Referência ao participante envolvido.         |
| `description`      | TEXT         |                             | Descrição da atividade realizada.             |
| **Chave Primária** | `id`         |                             |                                               |
| **Chave Estrangeira** | `report_id` | REFERENCES `reports(id)` ON DELETE CASCADE | |
| **Chave Estrangeira** | `participant_id` | REFERENCES `participants(id)` ON DELETE CASCADE | |

---

### Tabela: `photos`
Armazena fotos relacionadas aos relatórios.

| Coluna             | Tipo         | Restrição                   | Descrição                                       |
|--------------------|--------------|-----------------------------|-----------------------------------------------|
| `id`               | INT(11)      | NOT NULL, AUTO_INCREMENT    | Identificador único da foto.                  |
| `url`              | TEXT         |                             | URL da foto.                                  |
| `report_id`        | INT(11)      | NOT NULL                    | Referência ao relatório relacionado.          |
| **Chave Primária** | `id`         |                             |                                               |
| **Chave Estrangeira** | `report_id` | REFERENCES `reports(id)` ON DELETE CASCADE | |

---

## Restrições e Regras

1. **Chaves Primárias**  
   Cada tabela possui uma chave primária para garantir a unicidade dos registros.

2. **Chaves Estrangeiras**  
   - As relações entre tabelas são gerenciadas com chaves estrangeiras:
     - `reports.project_id` → `projects.id` (com `ON DELETE CASCADE`)
     - `reports.author` → `wp_users.ID` (com `ON DELETE SET NULL`)
     - `activities.report_id` → `reports.id` (com `ON DELETE CASCADE`)
     - `activities.participant_id` → `participants.id` (com `ON DELETE CASCADE`)
     - `photos.report_id` → `reports.id` (com `ON DELETE CASCADE`)

3. **Índices e Busca**  
   Indices e busca de texto completo são criados conforme necessidade do sistema.
   Por enquanto, só existe o index de full text search

---

