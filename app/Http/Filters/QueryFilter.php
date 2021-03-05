<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Request $request;
    protected Builder $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        // установка QueryBuilder
        $this->builder = $builder;

        // получим переменные Request обработаем по очереди
        foreach ($this->fields() as $field => $value) {
            // если метод доступен и данные не пусты
            if (method_exists($this, $field) and filled($value)) {

                // $this->$field($value);

                // получим данные о методе
                $reflectMethod = new \ReflectionMethod($this, $field);
                $parametersMethod = $reflectMethod->getParameters();
                // если метод публичный и есть хоть один аргумент
                if ($reflectMethod->isPublic() && count($parametersMethod) > 0) {
                    // начинаем разбор метода
                    foreach ($parametersMethod as $index => $args) {
                        // если есть класс создаем его экземпляр если нет то передаем данные
                        if ($args->getClass()) {
                            // получим имя класса
                            $class_name = $args->getClass()->getName();
                            // получим конструктор класса
                            $args_class = $args->getClass()->getMethod('__construct')->getParameters();
                            // если у конструктора для инициализации не нужны аргументы
                            if (count($args_class) == 0) {
                                $class = new $class_name();
                                $this->$field($class);
                            }
                            // если у конструктора для инициализации нужны аргументы и всего 1 Request
                            if (count($args_class) == 1) {
                                if ($args_class[0]->getClass()->getName() == Request::class) {
                                    $class = new $class_name(\request());
                                    $this->$field($class);
                                }
                            }
                        } else {
                            // определим тип данных
                            $type = $args->getType() ? $args->getType()->getName() : null;
                            // определим тип данных
                            if (!$type) {
                                // если типа данных нет
                                $this->$field($value);
                            } else {
                                if (gettype($value) != $type) {
                                    // если тип данных не совпал
                                    if ($type == 'array') {
                                        if (gettype($value) == 'string') {
                                            try {
                                                $this->$field(json_decode($value, true));
                                            } catch (\Exception $e){
                                                $this->$field([$value]);
                                            }
                                        }
                                        if (gettype($value) == 'int') {
                                            $this->$field([$value]);
                                        }
                                        if (gettype($value) == 'double') {
                                            $this->$field([$value]);
                                        }
                                    }
                                    if ($type == 'int') {
                                        if (gettype($value) == 'double') {
                                            $this->$field(floatval($value));
                                        }
                                        if (gettype($value) == 'string') {
                                            $this->$field(strval($value));
                                        }
                                    }
                                    if ($type == 'string') {
                                        if (gettype($value) == 'int') {
                                            $this->$field(intval($value));
                                        }
                                        if (gettype($value) == 'double') {
                                            $this->$field(floatval($value));
                                        }
                                    }

                                } else {
                                    // если тип данных совпал
                                    $this->$field($value);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    protected function fields(): array
    {
        // получение данных с Request
        return $this->request->all();
    }
}
