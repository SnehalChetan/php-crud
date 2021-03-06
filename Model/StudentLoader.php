<?php

class StudentLoader extends DbConnection
{
    private array $studentArr = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllStudents(): array
    {
        $students = $this->connect->query('select * from student')->fetchAll();
        foreach ($students as $student) {
            $teachArr = $this->fetchTeacherForStdnt($student['id']);
            $class = $this->fetchClassForStdnt($student['id']);
            $stdntObj = new Student ($student['id'], $student['firstname'], $student['lastname'],
            $student['email'], $student['address'], $class, $teachArr);
            array_push($this->studentArr, $stdntObj);

        }
        return $this->studentArr;
    }

    private function fetchTeacherForStdnt(int $id) : array
    {
        $query = 'select teacher_table.firstname
            from teacher_table , class_table , student
            where student.class_id = class_table.id and teacher_table.class_id = class_table.id
            and student.id =' . $id;
        return $this->connect->query($query)->fetchAll(PDO::FETCH_COLUMN);

    }

    private function fetchClassForStdnt(int $id) : string
    {
        $query = 'select class_table.name
        from  class_table , student
        where student.class_id = class_table.id
        and student.id = ' . $id;
        $class = $this->connect->query($query)->fetch();
        return $class[0];
    }

    public function deleteStudent(int $id): void{
        $query = 'delete from student where id =' . $id;
        $this->connect->query($query);
    }

    public function getClasses(): array{
        $sql = "select id, name
                from class_table";
        return $this->connect->query($sql)->fetchAll(/*PDO::FETCH_CLASS*/);
    }

    public function updateStudent(int $id , string $name,string $lastName,string $email,string $address ,int $classId): void{
       $sql = "update `student`
                set `firstname` = '".$name."', `lastname` ='".$lastName."', `email` ='".$email."' , `address` ='".$address."' , student.class_id =".$classId."
                where id = ".$id;
       $this->connect->query($sql);
    }

    public function addNewStudent(string $name, string $lastName,string $email,string $address,int $classId): void{
        $sql = "insert into student(`firstname`, `lastname`, `email`, `address`, `class_id`)
                values ('".$name."', '".$lastName."', '".$email."', '".$address."', ".$classId.")";
        $this->connect->query($sql);
    }

}