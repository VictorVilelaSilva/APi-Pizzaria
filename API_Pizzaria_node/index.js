const express = require('express');
const server = express();

const { alterarConteudo } = require('./functions');

// Isso é um comentário de linha

/*
Isso é um comentário de bloco
que pode se estender por várias
linhas.
*/


server.get('/1', (req, res) => {
    var Var = 'Contante var';
    let Let = 'Contante let';
    const Const = 5;
    console.log(Var + ' / ' + Let + ' / ' + Const);
    const { newVar, newLet, newConst } = alterarConteudo(Var, Let, Const);
    Var = newVar;
    Let = newLet;
    Const = newConst;
    // como estou tentar alterar o valor de uma constante, o código acima vai gerar um erro
    console.log(Var + ' / ' + Let + ' / ' + Const);

    }
);


server.get('/6', (req, res) => {

    let num1 = 5;
    let num2 = 10;
    let soma = num1 + num2;
    res.send('A soma de ' + num1 + ' + ' + num2 + ' é igual a ' + soma);
    }
);

server.get('/7', (req, res) => {
    const string1 = 'Olá, ';
    const string2 = 'mundo!';
    const string3 = string1 + string2
    res.send('string1: ' + string1 + ' || string2: ' + string2 + ' || concatenação: ' + string3);
    }
);

server.get('/8', (req, res) => {
    const num1 = 5;
    const num2 = '5';
    let comparacao = num1 === num2;
    res.send('A comparação entre ' + num1 + ' e ' + num2 + ' é ' + comparacao);
    }
);

server.get('/9', (req, res) => {
    let num = 5;
    num--;
    res.send('O decremento de 5 é igual a ' + num);
    }
);

server.get('/10', (req, res) => {
    // 10 13
    let num = 5;
    if(num >0 && num < 10){
        res.send('O número ' + num + ' está entre 0 e 10');
    }
    res.send('O número ' + num + ' não está entre 0 e 10');
}
);
server.get('/12', (req, res) => {
    let num = 5;
    let resposta = (num >0 && num < 10) ? 'O número ' + num + ' está entre 0 e 10' : 'O número ' + num + ' não está entre 0 e 10';
    res.send(resposta);
}
);

// ou ||
server.get('/11', (req, res) => {
    let num = 5;
    if(num < 0 || num > 10){
        res.send('O número ' + num + ' está fora do intervalo de 0 a 10');
    }
    res.send('O número ' + num + ' está dentro do intervalo de 0 a 10');
}
);

//switch
server.get('/14', (req, res) => {
    let num = 5;
    switch(num){
        case 1:
            res.send('O número é 1');
            break;
        case 2:
            res.send('O número é 2');
            break;
        case 3:
            res.send('O número é 3');
            break;
        default:
            res.send('O número não é 1, 2 ou 3');
            break;
    }
}
);

//for
server.get('/15', (req, res) => {
    let texto = '';
    for(let i = 0; i < 10; i++){
        texto += i + ' ';
    }
    res.send(texto);
}
);

//while
server.get('/16', (req, res) => {
    let texto = '';
    let i = 0;
    while(i < 10){
        texto += i + ' ';
        i++;
    }
    res.send(texto);
}
);

//map
server.get('/17', (req, res) => {
    let array = [1,2,3,4,5];
    let arrayDobrado = array.map((elemento) => {
        return elemento * 2;
    });
    res.send(arrayDobrado);
}
);

server.get('/18', (req, res) => {
    //18 20
    let array = [1,2,3,4,5];
    let texto = '';
    array.forEach((elemento) => {
        texto += elemento + ' ';
    });
    res.send(texto);
}
);

server.get('/19', (req, res) => {
    //19 21 
    let objeto = {nome: 'João', idade: 25, cidade: 'São Paulo'};
    let texto = '';
    for(let propriedade in objeto){
        texto += propriedade + ': ' + objeto[propriedade] + ' ';
    }
    res.send(texto);
}
);

//utilize a função set
server.get('/22', (req, res) => {
    const valoresConsulta = ['1', '2', '3', '3', '4','4'].join(','); 
    const arrayValores = valoresConsulta.split(',');

    const conjuntoValoresUnicos = new Set(arrayValores);

    const arrayValoresUnicos = Array.from(conjuntoValoresUnicos);

    res.send(arrayValoresUnicos);
});

server.get('/23', (req, res) => {
    const objeto1 = {nome: 'João', idade: 25, cidade: 'São Paulo'};
    const objeto2 = {nome: 'João', idade: 25, cidade: 'São Paulo'};
    const util = require('util');
    const comparacao = util.isDeepStrictEqual(objeto1, objeto2);
    res.send('Os objetos são iguais? ' + comparacao);
});

server.get('/24', (req, res) => {
    const nome = 'João';
    const idade = 25;
    const cidade = 'São Paulo';
    const texto = `Nome: ${nome} || Idade: ${idade} || Cidade: ${cidade}`;
    res.send(texto);z
});

server.get('/25', (req, res) => {
    const objeto = {nome: 'João', idade: 25, cidade: 'São Paulo'};
    const cidade = objeto?.cidade;
    res.send('Cidade: ' + cidade);
});



server.listen(3000, () => {
    console.log('API Online');
    }
);