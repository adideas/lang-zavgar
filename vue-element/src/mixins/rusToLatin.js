export function rus_to_latin(str) {
  var ru = {
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
    'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
    'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
    'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
    'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
    'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'u', 'я': 'ya'
  }; var n_str = []

  str = str.replace(/[ъь]+/g, '').replace(/й/g, 'i')

  for (var i = 0; i < str.length; ++i) {
    n_str.push(
      ru[ str[i] ] ||
      ru[ str[i].toLowerCase() ] === undefined && str[i] ||
      ru[ str[i].toLowerCase() ].toUpperCase()
    )
  }

  // eslint-disable-next-line no-extend-native
  const Str = String
  Str.prototype.withoutSymbol = function() {
    return this.replace(/[~,`,#,!,?,:,.,\-,=,(,),+,№,*,&,|,\,,>,<,@,\",\',1,2,3,4,5,6,7,8,9,0,$,%, ,^,\r,\\,\/,\t,&,\},\{,\[,\]]*/g, '')
      .replace(/[а-я]/g, '')
      .replace(/[А-Я]/g, '')
      .replace(/[\n]/g, '')
  }
  return new Str(
    n_str.join('')
  )
}
