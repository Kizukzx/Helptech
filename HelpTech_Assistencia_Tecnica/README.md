# HelpTech — sistema de assistência técnica

Projeto PHP + MySQL baseado na estrutura visual do sistema enviado. Foram incluídos login, expiração de sessão após 30 minutos, painel principal, busca de clientes, CRUD de clientes/equipamentos/ordens de serviço e ordenação das OS por data de abertura.

## Instalação
1. Copie a pasta `sistema` para o diretório público do Apache (ex.: `htdocs/helptech`).
2. Edite `sistema/config/conexao.php` e informe servidor, usuário, senha, banco e porta do banco que você já criou.
3. Confira se o banco possui as tabelas e colunas esperadas abaixo. O SQL não foi substituído porque você informou que já criou o banco.
4. Acesse `http://localhost/helptech/`.

## Estrutura de banco esperada pelo código
- `usuarios`: `id`, `nome`, `email`, `senha`, `perfil` (a senha pode ser um hash de `password_hash`; o código mantém compatibilidade temporária com senhas em texto puro).
- `clientes`: `id`, `nome`, `cpf`, `telefone`, `email`, `endereco`.
- `equipamentos`: `id`, `cliente_id`, `tipo`, `marca`, `modelo`, `numero_serie`, `descricao`.
- `ordens_servico`: `id`, `equipamento_id`, `descricao_problema`, `diagnostico`, `servico_realizado`, `status`, `valor`, `data_abertura`, `data_conclusao`.

**Importante:** o SQL que veio dentro do ZIP original cria `usuarios` e `chamados` (HelpTech de chamados de suporte), enquanto o PDF pede clientes, equipamentos e ordens de serviço. Por isso, os arquivos desta versão foram preparados para o esquema descrito acima. Se as tabelas que você já criou tiverem nomes/colunas diferentes, será necessário ajustar as consultas PHP para corresponder exatamente ao seu banco.

## Segurança
- Consultas de escrita e busca usam prepared statements.
- A sessão é renovada após login e expira após 30 minutos de inatividade.
- O CPF é criptografado no cadastro com AES-256-CBC. Mantenha a chave `APP_ENCRYPTION_KEY` em segredo e faça backup dela: sem a mesma chave os CPFs existentes não poderão ser descriptografados. Para dados antigos em texto puro, a tela ainda os exibe como estão.
- Em produção, use HTTPS, guarde a chave fora do código-fonte e migre todas as senhas para `password_hash()`.


## Portal separado por perfil (atualização)
- **Cliente:** após entrar, é direcionado para `sistema/cliente/index.php`, onde pode abrir chamados e acompanhar status e respostas da equipe.
- **Administrador/técnico/trabalhador:** continua no painel interno e tem o link **Chamados dos clientes** para atualizar o status e enviar respostas.
- Execute `banco/migracao_portal_cliente.sql` no banco `helptech` já existente. A migração cria apenas a tabela `chamados_cliente` e uma conta de demonstração, sem apagar tabelas ou registros.
- **Login de demonstração do cliente:** `cliente@helptech.local` / `Cliente@123`.
- Os usuários da equipe continuam usando suas contas existentes. O campo `usuarios.perfil` deve ser `Administrador`, `Técnico` (ou `Trabalhador`) ou `Cliente`.


## Atualização: especificações e painel técnico integrado
- O formulário do cliente agora pede tipo do eletrônico, marca, modelo, número de série (opcional), estado físico, acessórios e descrição detalhada do defeito.
- A equipe usa `sistema/atendimentos.php` para consultar e atualizar chamados de clientes e ordens de serviço em uma única tela. O painel técnico aponta para essa área integrada.
- O cadastro manual de novas ordens internas foi desativado; a equipe pode atualizar ordens existentes. Os clientes continuam podendo abrir chamados pelo portal.
- As páginas antigas de listagem de chamados e ordens redirecionam para a área integrada.
- Execute novamente `banco/migracao_portal_cliente.sql` para adicionar, de forma idempotente, as colunas das especificações. A migração não apaga dados existentes.
- Não existe tela de cadastro público de usuários; o acesso continua restrito a contas existentes e à criação de chamados pela área do cliente.
