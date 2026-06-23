<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $activeTab = 'general';
    public $default_tax;
    public $store_name;
    public $store_phone;
    public $store_address;
    public $receipt_footer;

    // Receipt customization
    public $receipt_width = '80mm';
    public $receipt_show_address = true;
    public $receipt_show_phone = true;
    public $receipt_show_tax = true;
    public $receipt_show_discount = true;
    public $receipt_show_payment_method = true;
    public $receipt_show_change = true;
    public $receipt_show_sku = false;
    public $receipt_header = '';

    public $lastBackup = null;
    public $backupMessage = null;
    public $backupError = false;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->store_name = Setting::getValue('store_name', config('app.name'));
        $this->default_tax = Setting::getValue('default_tax', '11');
        $this->store_phone = Setting::getValue('store_phone', '');
        $this->store_address = Setting::getValue('store_address', '');
        $this->receipt_footer = Setting::getValue('receipt_footer', 'Terima kasih telah berbelanja di toko kami.');
        $this->receipt_width = Setting::getValue('receipt_width', '80mm');
        $this->receipt_show_address = Setting::getValue('receipt_show_address', '1') === '1';
        $this->receipt_show_phone = Setting::getValue('receipt_show_phone', '1') === '1';
        $this->receipt_show_tax = Setting::getValue('receipt_show_tax', '1') === '1';
        $this->receipt_show_discount = Setting::getValue('receipt_show_discount', '1') === '1';
        $this->receipt_show_payment_method = Setting::getValue('receipt_show_payment_method', '1') === '1';
        $this->receipt_show_change = Setting::getValue('receipt_show_change', '1') === '1';
        $this->receipt_show_sku = Setting::getValue('receipt_show_sku', '0') === '1';
        $this->receipt_header = Setting::getValue('receipt_header', '');
        $this->lastBackup = Setting::getValue('last_backup', null);
    }

    protected function rules()
    {
        return [
            'store_name' => 'required|string|max:100',
            'default_tax' => 'required|numeric|min:0|max:100',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:500',
            'receipt_header' => 'nullable|string|max:500',
            'receipt_width' => 'required|in:40mm,58mm,76mm,80mm',
        ];
    }

    public function save()
    {
        $this->validate();

        Setting::setValue('store_name', $this->store_name);
        Setting::setValue('default_tax', $this->default_tax);
        Setting::setValue('store_phone', $this->store_phone);
        Setting::setValue('store_address', $this->store_address);
        Setting::setValue('receipt_footer', $this->receipt_footer);
        Setting::setValue('receipt_width', $this->receipt_width);
        Setting::setValue('receipt_show_address', $this->receipt_show_address ? '1' : '0');
        Setting::setValue('receipt_show_phone', $this->receipt_show_phone ? '1' : '0');
        Setting::setValue('receipt_show_tax', $this->receipt_show_tax ? '1' : '0');
        Setting::setValue('receipt_show_discount', $this->receipt_show_discount ? '1' : '0');
        Setting::setValue('receipt_show_payment_method', $this->receipt_show_payment_method ? '1' : '0');
        Setting::setValue('receipt_show_change', $this->receipt_show_change ? '1' : '0');
        Setting::setValue('receipt_show_sku', $this->receipt_show_sku ? '1' : '0');
        Setting::setValue('receipt_header', $this->receipt_header);

        activity()->log('Pengaturan toko diperbarui');

        $this->dispatch('settings-saved');
        session()->flash('success', 'Pengaturan berhasil disimpan!');
    }

    public function backupDatabase()
    {
        $this->backupError = false;
        $this->backupMessage = null;

        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::connection()->getDatabaseName();
            $key = 'Tables_in_' . $dbName;

            $sql = "-- ===========================================\n";
            $sql .= "-- Database Backup: " . $dbName . "\n";
            $sql .= "-- Tanggal: " . now()->format('d/m/Y H:i:s') . "\n";
            $sql .= "-- Aplikasi: " . config('app.name') . "\n";
            $sql .= "-- ===========================================\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$key;

                $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
                $createSql = $createTable[0]->{'Create Table'};

                $sql .= "-- -----------------------------------------\n";
                $sql .= "-- Table: `$tableName`\n";
                $sql .= "-- -----------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
                $sql .= $createSql . ";\n\n";

                $rows = DB::table($tableName)->get();
                if ($rows->isNotEmpty()) {
                    $cols = array_keys(get_object_vars($rows[0]));
                    $colNames = implode('`, `', $cols);
                    $sql .= "INSERT INTO `$tableName` (`$colNames`) VALUES\n";

                    $values = [];
                    foreach ($rows as $row) {
                        $vals = [];
                        foreach ($cols as $col) {
                            $val = $row->$col;
                            if (is_null($val)) {
                                $vals[] = 'NULL';
                            } elseif (is_int($val) || is_float($val)) {
                                $vals[] = $val;
                            } else {
                                $vals[] = "'" . str_replace(["'", "\\"], ["''", "\\\\"], $val) . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $vals) . ')';
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $sql .= "-- Backup completed: " . now()->format('d/m/Y H:i:s') . "\n";

            $filename = 'backup-' . now()->format('Ymd-His') . '.sql';

            Setting::setValue('last_backup', now()->format('Y-m-d H:i:s'));
            $this->lastBackup = now()->format('Y-m-d H:i:s');

            activity()->log('Database backup berhasil diunduh');

            return response()->streamDownload(function () use ($sql) {
                echo $sql;
            }, $filename);

        } catch (\Exception $e) {
            $this->backupError = true;
            $this->backupMessage = 'Gagal membuat backup: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.setting.settings')
            ->layout('layouts.app', ['title' => 'Pengaturan']);
    }
}
