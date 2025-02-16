// importar biblioteca necessaria
require("dotenv").config();
const express = require("express");
const bodyParse = require("bodyParser");
const cors = require("cors");

// inicializa o servidor express
const app = express();

//configura o servidor para aceitar o json e permitir conexoes externas
app.use(cors());  //permite que o front end se comunique com o back end
app.use(bodyParse.json()); //permite que o servidor leia a requisições json

//simula um banco de dados de usuarios
const users =
[
    {
        email:"teste@email.com", password: "123456" //exemplo de usuario 
    }
]

//rota para login
app.post("/login", (req, res) =>
    {
        const {email, password} = req.body;
        const user = users.find( u=> u.email === email && u.password === password);
        if(user)
        {
            res.status(200).json({ message: "login bem sucedido"});

        }
        else 
        {
            res.status(401).json({ message: "Credenciais invalidas"});
        }
   
    });
//define a porta do servidor e inicia o backend
const PORT = process.env.PORT || 5000;

console.log("O codigo rodou ate o fim");

app.get('/', (req, res) => {
    res.send("Servidor está funcionando!");
});

app.listen(PORT, () =>
{
    
    
    console.log(`servidor rodando na porta ${PORT}`);
});



