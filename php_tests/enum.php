<?php 

    enum Checker : string
    {
        case Pending = "$300";
        case Paid = "$500";
        case Void = "$0";
        case Fail = "$10";

    }

    // echo Checker::Pending->name ." : ";
    // echo Checker::Pending->value . "<br>";
    // var_dump(Checker::cases());
    // echo json_encode(Checker::cases()) . "<br>";

    enum enum2
    {

        case Pending;
        case Paid;
        case Void;
        case Fail;

        public static function enum2_data(self $value):string{

            return match ($value) {
                enum2::Pending => '$300',
                enum2::Paid => '$500',
                enum2::Void => '$0',
                enum2::Fail => '$10',
            };

        }

    }

    // echo "Running: " . enum2::enum2_data(enum2::Pending);

    echo json_encode([
            "pending"=>enum2::enum2_data(enum2::Pending),
            "paid"=>enum2::enum2_data(enum2::Paid),
            "void"=>enum2::enum2_data(enum2::Void)
        ]);

?>