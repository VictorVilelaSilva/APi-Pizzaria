function alterarConteudo(variavel1,variavel2,variavel3){
    variavel1 = variavel1 + ' alterado';
    variavel2 = variavel2 + ' alterado';
    variavel3 = variavel3 + ' alterado';
    let arrayVariavel = [variavel1,variavel2,variavel3];

    return arrayVariavel;

}


module.exports = { alterarConteudo };