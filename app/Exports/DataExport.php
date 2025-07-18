namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DataExport implements FromView
{
    protected $data;

    // Pass the processed data through the constructor
    public function __construct($data)
    {
        $this->data = $data;
    }

    // Export data using a Blade view
    public function view(): View
    {
        return view('exports.data', [
            'data' => $this->data
        ]);
    }
}

