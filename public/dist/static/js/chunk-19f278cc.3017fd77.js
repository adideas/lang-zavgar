(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-19f278cc"],{"2d0b":function(t,e,a){"use strict";var s=a("69e8"),o=a.n(s);o.a},"69e8":function(t,e,a){},9406:function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"app-container",staticStyle:{background:"#e0e0e0","min-height":"calc(100vh - 84px)"}},[t.$store.getters.access.root?a("div",[a("div",{staticClass:"el-col-xs-24 el-col-sm-11 el-col-sm-offset-1 el-col-md-11 el-col-md-offset-1 el-col-lg-11 el-col-lg-offset-1 el-col-xl-11 el-col-xl-offset-1 el-col-12",staticStyle:{"margin-top":"10px"}},[a("el-card",{attrs:{header:"Статус перевода ключей"}},[a("div",{staticClass:"el-col-24"},[a("div",{staticClass:"el-col-12 my_dashboard_class success",staticStyle:{"font-size":"30px","font-weight":"700"}},[a("vue-count-to",{attrs:{"start-val":0,"end-val":t.data.key_status[0]}})],1),t._v(" "),a("div",{staticClass:"el-col-12 my_dashboard_class warning",staticStyle:{"font-size":"30px","font-weight":"700"}},[a("vue-count-to",{attrs:{"start-val":0,"end-val":t.data.key_status[1]}})],1),t._v(" "),a("div",{staticClass:"el-col-12 my_dashboard_class success"},[t._v("\n                        Всего\n                    ")]),t._v(" "),a("div",{staticClass:"el-col-12 my_dashboard_class warning"},[t._v("\n                        Без перевода\n                    ")])])])],1),t._v(" "),t._l(t.data.git,(function(e,s){return a("div",{key:s,staticClass:"el-col-xs-24 el-col-sm-11 el-col-sm-offset-1 el-col-md-11 el-col-md-offset-1 el-col-lg-11 el-col-lg-offset-1 el-col-xl-11 el-col-xl-offset-1 el-col-12",staticStyle:{"margin-top":"10px"}},[a("el-card",[a("template",{slot:"header"},[a("span",[t._v("\n          "+t._s(s+" ("+e.branches.join(", ")+")")+"\n        ")]),t._v(" "),a("el-button",{staticStyle:{float:"right"},attrs:{size:"mini",type:"info"},on:{click:function(e){return t.uploadToGithubDev(s)}}},[t._v("push develop")]),t._v(" "),a("el-button",{staticStyle:{float:"right"},attrs:{size:"mini",type:"danger"},on:{click:function(e){return t.uploadToGithub(s)}}},[t._v("push master")])],1),t._v(" "),a("el-card",{attrs:{header:"Последняя фиксация"}},[a("code",{staticClass:"hljs bash",staticStyle:{"white-space":"pre"},domProps:{innerHTML:t._s(t.hljs_view(e.last_commit))}})]),t._v(" "),a("el-card",{staticStyle:{"margin-top":"10px"},attrs:{header:"Статус"}},[a("code",{staticClass:"hljs bash",staticStyle:{"white-space":"pre"},domProps:{innerHTML:t._s(t.hljs_view(e.status))}})])],2)],1)}))],2):a("div",[a("el-card",{attrs:{header:"Перевод"}},[a("p",[t._v("Добро пожаловать в систему переводов Zavgar.Online")]),t._v(" "),a("p",[t._v("Что бы начать переводить ключи (текст) нажмите на кнопку приступить к переводу")]),t._v(" "),a("div",{staticClass:"el-col-24",staticStyle:{margin:"20px 0"}},[a("el-button",{staticStyle:{float:"right"},attrs:{type:"danger"},on:{click:function(e){return t.$router.push({name:"Translate"})}}},[t._v("Приступить к переводу")])],1)])],1),t._v(" "),a("el-dialog",{attrs:{title:"Отправка на github","append-to-body":"",visible:t.dialog_upload_git,width:"170px"},on:{"update:visible":function(e){t.dialog_upload_git=e}}},[a("el-progress",{attrs:{type:"dashboard",percentage:t.percentage,color:t.colors}})],1)],1)},o=[],l=a("0a66"),c=a("1487"),n=a.n(c),i=(a("2c43"),a("ec1b")),r=a.n(i),d={name:"Dashboard",components:{VueCountTo:r.a},data:function(){return{data:{},dialog_upload_git:!1,percentage:10,colors:[{color:"#f56c6c",percentage:20},{color:"#e6a23c",percentage:40},{color:"#5cb87a",percentage:60},{color:"#1989fa",percentage:80},{color:"#6f7ad3",percentage:100}]}},created:function(){var t=this;this.$store.getters.access.root?this.getDashboard():setTimeout((function(){t.$store.getters.access.root&&t.getDashboard()}),1e3)},methods:{uploadToGithub:function(t){var e=this;this.$store.getters.access.root&&this.$alert("Отправить код на github (master ветку) ?","Внимание",{confirmButtonText:"Отправить",showCancelButton:!0,cancelButtonText:"Отменить"}).then((function(a){Object(l["e"])("translate",{repo:t,type:"master"}).then((function(t){e.dialog_upload_git=!0;var a=setInterval((function(){e.percentage+=2,e.percentage>=100&&(e.percentage=0,e.dialog_upload_git=!1,clearInterval(a))}),200)}))}))},uploadToGithubDev:function(t){var e=this;this.$store.getters.access.root&&this.$alert("Отправить код на github (develop ветку) ?","Внимание",{confirmButtonText:"Отправить",showCancelButton:!0,cancelButtonText:"Отменить"}).then((function(a){e.dialog_upload_git=!0;var s=setInterval((function(){e.percentage+=2,e.percentage>=100&&(e.percentage=0,e.dialog_upload_git=!1,clearInterval(s))}),200);Object(l["e"])("translate",{repo:t,type:"develop"}).then((function(t){}))}))},getDashboard:function(){var t=this;Object(l["b"])("dashboard").then((function(e){t.data=e.data.data}))},hljs_view:function(t){return Array.isArray(t)?n.a.highlight("bash",t.join("\n")).value:n.a.highlight("bash",t).value}}},u=d,h=(a("2d0b"),a("2877")),p=Object(h["a"])(u,s,o,!1,null,"240e1e26",null);e["default"]=p.exports}}]);