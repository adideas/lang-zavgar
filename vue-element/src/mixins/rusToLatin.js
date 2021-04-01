// Если с английского на русский, то передаём вторым параметром true.
var rus = 'щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь'.split(/ +/g)
var eng = 'shh sh ch cz yu ya yo zh zz ii ye a b v g d e z i j k l m n o p r s t u f x z'.split(/ +/g)

export function transliterate(text, engToRus) {
  text = engToRus ? text.replace(/_/g, ' ') : text
  var x
  for (x = 0; x < rus.length; x++) {
    text = text.split(engToRus ? eng[x] : rus[x]).join(engToRus ? rus[x] : eng[x])
    text = text.split(engToRus ? eng[x].toUpperCase() : rus[x].toUpperCase()).join(engToRus ? rus[x].toUpperCase() : eng[x].toUpperCase())
  }
  return engToRus ? text : text.replace(/ /g, '_')
}

export function rus_to_latin(str) {
  const Str = String
  Str.prototype.withoutSymbol = function() {
    return this.replace(/[~,`,#,!,?,:,.,\-,=,(,),+,№,*,&,|,\,,>,<,@,\",\',1,2,3,4,5,6,7,8,9,0,$,%, ,^,\r,\\,\/,\t,&,\},\{,\[,\]]*/g, '')
      .replace(/[а-я]/g, '')
      .replace(/[А-Я]/g, '')
      .replace(/[\n]/g, '')
  }
  return new Str(
    transliterate(str)
  )
}
