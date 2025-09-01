# Ponderada de Prgoramação Semana 4
Este repositório contém arquivos PHP para gerenciar bancos de dados de carros e funcionários, projetados para serem executados em uma instância EC2 da AWS com um servidor web Apache e um banco de dados MariaDB, MySQL ou PostgreSQL. O link com a demosntração dessa aplicação pode ser encontrado em: https://drive.google.com/file/d/1B_eRc77D51VmYo5BMK0nyRxw-1d7i3s6/view?usp=sharing

## Configurando o Ambiente na Instância EC2

Para utilizar estes arquivos, você precisará de uma instância EC2 com um servidor web Apache e PHP instalados. O guia a seguir é baseado no tutorial da AWS "Install a web server on your EC2 instance".

### Passos para a Configuração

1.  **Conecte-se à sua instância EC2:** Utilize SSH para acessar sua instância.

2.  **Atualize os pacotes:** Garanta que sua instância esteja com os pacotes mais recentes.

    ```bash
    sudo dnf update -y
    ```

3.  **Instale o servidor web Apache, PHP e MariaDB:** 

    ```bash
    sudo dnf install -y httpd php php-mysqli mariadb105
    ```

4.  **Inicie e habilite o servidor Apache:** 
    ```bash
    sudo systemctl start httpd
    sudo systemctl enable httpd
    ```

5.  **Ajuste as permissões do diretório web:** Para permitir que o usuário `ec2-user` gerencie os arquivos no diretório raiz do Apache, execute os seguintes comandos:

    ```bash
    sudo usermod -a -G apache ec2-user
    exit
    # Faça o login novamente
    groups
    sudo chown -R ec2-user:apache /var/www
    sudo chmod 2775 /var/www
    find /var/www -type d -exec sudo chmod 2775 {} \;
    find /var/www -type f -exec sudo chmod 0664 {} \;
    ```

6.  **Crie o arquivo de configuração do banco de dados:**

      * Crie um diretório chamado `inc` dentro de `/var/www`.
      * Dentro de `/var/www/inc`, crie um arquivo chamado `dbinfo.inc` com o seguinte conteúdo, substituindo os valores de exemplo pelas suas credenciais do banco de dados:
        ```php
        <?php
        define('DB_SERVER', 'your_db_instance_endpoint');
        define('DB_USERNAME', 'your_username');
        define('DB_PASSWORD', 'your_password');
        define('DB_DATABASE', 'your_database_name');
        ?>
        ```

7.  **Envie os arquivos PHP para a instância:**

      * Coloque os arquivos `CarsManager.php` e `SamplePage.php` no diretório `/var/www/html` da sua instância EC2.

8.  **Acesse as páginas:**

      * Para o gerenciador de carros, acesse: `http://<seu-ec2-endpoint>/CarsManager.php`
      * Para a página de exemplo de funcionários, acesse: `http://<seu-ec2-endpoint>/SamplePage.php` 

## Detalhes das Tabelas do Banco de Dados

Os scripts PHP criarão automaticamente as seguintes tabelas no banco de dados se elas não existirem.

### Tabela: `CARS` (Gerenciada por `CarsManager.php`)

| Coluna | Tipo de Dado | Descrição |
| --- | --- | --- |
| `ID` | `INT(11) UNSIGNED AUTO_INCREMENT` | Chave primária, identificador único para cada carro. |
| `BRAND` | `VARCHAR(50)` | Marca do carro. |
| `MODEL` | `VARCHAR(50)` | Modelo do carro. |
| `YEAR` | `INT(4)` | Ano de fabricação do carro. |
| `PRICE` | `DECIMAL(10, 2)` | Preço do carro. |
| `DATE_ADDED` | `TIMESTAMP` | Data e hora em que o registro foi adicionado. |

### Tabela: `EMPLOYEES` (Gerenciada por `SamplePage.php`)

| Coluna | Tipo de Dado | Descrição |
| --- | --- | --- |
| `ID` | `INT(11) UNSIGNED AUTO_INCREMENT` | Chave primária, identificador único para cada funcionário. |
| `NAME` | `VARCHAR(45)` | Nome do funcionário. |
| `ADDRESS` | `VARCHAR(90)` | Endereço do funcionário. |