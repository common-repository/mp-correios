=== mp-correios ===
Contributors: Thiago Quadros
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RW99LTSBNV3DS
Tags: marketpress,shipping,correios,plugins-shipping,marketplace,pac,sedex,sedex 10,sedex hoje,sedex a cobrar,frete,delivery
Requires at least: 3.5
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Correios shipping to the Marketpress plugin

== Description ==

### Add Correios shipping to Marketpress ###

This plugin adds Correios shipping to Marketpress.

Please notice that Marketpress must be installed and active.

### Descrição em Português: ###

Adicione os Correios como método de entrega em sua loja Marketpress.

[Correios](http://www.correios.com.br/) é um método de entrega brasileiro.

O plugin mp-correios foi desenvolvido sem nenhum incentivo dos Correios. Nenhum dos desenvolvedores deste plugin possuem vínculos com esta empresa.

Este plugin foi feito baseado na documentação do [Webservices Correios](http://www.correios.com.br/webservices/).

= Métodos de entrega aceitos: =

* PAC (sem contrato).
* SEDEX (sem contrato).
* SEDEX 10.
* SEDEX Hoje.
* SEDEX a cobrar.

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* Entre em contato com os desenvolvedores do plugin [página](http://www.artboxstudio.net/).
* Entre em contato através do email [email](tmquadros@hotmail.com).

== Installation ==

* Upload plugin files to your plugins-shipping folder of Marketpress;
* Eg. C:\<project path>\wp-content\plugins\marketpress\marketpress-includes\plugins-shipping
* Activate the plugin;
* http://<project url>/wp-admin/edit.php?post_type=product&page=marketpress&tab=shipping
* Select "Calculate Options"
* Select "Correios"
* Select services PAC, SEDEX, etc
* Insert the Base Zip
* Save

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Após baixar o arquivo, insira-o dentro do diretório de meios de envio do Marketpress
* Ex: C:\<caminho do projeto>\wp-content\plugins\marketpress\marketpress-includes\plugins-shipping
* Depois habilite no seguinte link:
* http://<link do projeto>/wp-admin/edit.php?post_type=product&page=marketpress&tab=shipping
* Selecione o meio de envio "Opções de Cálculo"
* Selecione as opções de envio e habilite "Correios"
* Selecione os Serviços Nacionais como PAC, SEDEX, etc
* Informe o CEP origem
* Salve

= Requerimentos: =

Possuir instalado a extensão SimpleXML (que já é instalado por padrão com o PHP 5).

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin Marketpress.
* Possuir instalado em sua hospedagem a extensão de SimpleXML.
* Configurar o seu CEP de origem nas configurações do plugin.
* Adicionar peso e dimensões nos produtos que pretende entregar.

**Atenção**: É obrigatório ter o **peso** configurado em cada produto para que seja possível cotar o frete de forma eficiente.

= Quais são os métodos de entrega que o plugin aceita? =

São aceitos os métodos:

* PAC (sem contrato, código 41106).
* SEDEX (sem contrato, código 40010).
* SEDEX 10 (código 40215).
* SEDEX Hoje (código 40290).
* SEDEX a cobrar (código 40045)

Para mais informações sobre os métodos de entrega dos Correios visite: [Encomendas - Correios](http://www.correios.com.br/voce/enviar/encomendas.cfm).

= Como é feita a cotação do frete? =

A cotação do frete é feita utilizando o [Webservices dos Correios](http://www.correios.com.br/webservices/) utilizando SimpleXML (que é nativo do PHP 5).

Na cotação do frete é usado o seu CEP de origem, CEP de destino do cliente e a cubagem total dos produtos mais o peso. Desta forma o valor cotado sera o mais próximo possível do real.

Desta forma é necessário adicionar pelo menos o peso em cada produto, pois na falta de dimensões serão utilizadas as configurações do pacote padrão.

= É possível calcular frete para quais países? =

No momento o Webservices faz cotação apenas para dentro do Brasil.

= Quais são os limites de dimensões e peso do plugin? =

Veja quais são os limites em: [Correios - limites de dimensões e peso](http://www.correios.com.br/produtosaz/produto.cfm?id=8560360B-5056-9163-895DA62922306ECA).

= Os métodos de entrega dos Correios não aparecem durante o checkout ou no carrinho? =

Verifique se você realmente ativou as opções de entrega do plugin.

Além de conferir se o carrinho possue produtos do tipo **simples** e **variável** e não estarem marcados com *virtual* ou *baixável*.

= O valor do frete calculado não bateu com a da loja dos Correios? =

Este plugin utiliza o Webservices dos Correios para calcular o frete e quando este tipo de problema acontece geralmente é porque:

1. Foram configuradas de forma errada as opções de peso e dimensões dos produtos na loja.
2. O Webservices dos Correios enviou um valor errado! Sim isso acontece e na página da documentação do Webservices tem o seguinte alerta:

>Os valores obtidos pelos simuladores aqui disponíveis são uma estimativa e deverão ser confirmados no ato da postagem.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Entre em contato [email](tmquadros@hotmail.com)

== Screenshots ==

1. Settings page.

== Changelog ==

= 0.1 - 01/04/2014 =

* Finalizado o desenvolvimento do plugin

== Upgrade Notice ==

* Nothing

== About us ==

www.artboxstudio.net
Our contact address: contato@artboxstudio.net

== License ==

mp-correios is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

mp-correios is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with mp-correios. If not, see <http://www.gnu.org/licenses/>.