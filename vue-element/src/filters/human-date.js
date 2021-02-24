
const months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря']
const monthsShort = ['янв.', 'фев.', 'мар.', 'апр.', 'мая', 'июня', 'июля', 'авг.', 'сент.', 'окт.', 'нояб.', 'дек.']

/**
 * @param laravel_date - даты из Ларавел в формате 2020-02-06 10:29:00  2020-02-07 12:03:26
 * @returns {string} - человекопонятная дата
 */
export default function(laravel_date = '1900-01-01 01:01:01', with_time = true) {
  var now = new Date()

  var my_date = new Date(laravel_date)

  const between = (Date.now() - my_date.getTime()) / 1000

  if (between < 60 && 0) {
    // формат -  Только что
    return 'Только что '
  } else if (between < 3600 && 0) {
    // формат -  5 мин назад
    return ~~(between / 60) + ' мин назад'
  } else {
    if (now.toDateString() === my_date.toDateString()) {
      // формат -  Сегодня 19:08
      return `Сегодня ${laravel_date.slice(11, 13)}:${laravel_date.slice(14, 16)}`
    } else if (now.toDateString() === new Date(+my_date + (1000 * 60 * 60 * 24)).toDateString()) {
      // формат -  Вчера 09:06
      return `Вчера ${laravel_date.slice(11, 13)}:${laravel_date.slice(14, 16)}`
    } else if (my_date.getFullYear() === now.getFullYear()) {
      // формат (текущий год) -  6 февраля 17:16
      return `${my_date.getDate()} ${months[my_date.getMonth()]}  ${laravel_date.slice(11, 13)}:${laravel_date.slice(14, 16)}`
    } else {
      var time = with_time ? `${laravel_date.slice(11, 13)}:${laravel_date.slice(14, 16)}` : ''
      // формат (не текущий год) -  6 февр. 2019 г. 17:16
      return `
        ${my_date.getDate()}
        ${monthsShort[my_date.getMonth()]}
        ${my_date.getFullYear()} г.
        ${time}
      `
    }
  }
}
