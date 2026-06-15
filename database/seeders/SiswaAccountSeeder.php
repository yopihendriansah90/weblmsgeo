<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SiswaAccountSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $siswaRole = Role::findOrCreate('siswa');
            $usernameOccurrences = [];

            foreach ($this->rows() as $index => $row) {
                $baseUsername = $this->normalizeUsername($row['User Name'] ?? 'siswa'.($index + 1));
                $username = $this->usernameForOccurrence($baseUsername, $usernameOccurrences);
                $school = $this->school($row['Sekolah'] ?? 'Sekolah Tidak Diketahui');

                $user = User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $this->studentName($row),
                        'email' => null,
                        'password' => $this->clean($row['Password'] ?? 'password'),
                        'status' => 'active',
                    ],
                );

                $user->syncRoles([$siswaRole]);

                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'school_id' => $school->id,
                        'nisn' => null,
                        'class_name' => null,
                        'status' => 'active',
                    ],
                );
            }
        });
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function rows(): array
    {
        $file = new \SplFileObject(base_path('asssets/akun siswa.csv'));
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $headers = [];
        $rows = [];

        foreach ($file as $line) {
            if (! is_array($line) || $this->isEmptyRow($line)) {
                continue;
            }

            if ($headers === []) {
                if ($this->clean((string) ($line[0] ?? '')) !== 'No.') {
                    continue;
                }

                $headers = array_map(fn ($value): string => $this->clean((string) $value), $line);
                continue;
            }

            $rows[] = array_combine($headers, array_pad($line, count($headers), null));
        }

        return $rows;
    }

    private function studentName(array $row): string
    {
        $firstName = $this->clean($row['Nama Depan'] ?? '');
        $lastName = $this->clean($row['Nama Belakang'] ?? '');

        return trim($firstName.' '.$lastName) ?: $this->clean($row['User Name'] ?? 'Siswa');
    }

    private function school(?string $name): School
    {
        $name = $this->clean((string) $name) ?: 'Sekolah Tidak Diketahui';

        return School::updateOrCreate(
            ['name' => $name],
            [
                'code' => Str::upper(Str::slug($name, '')),
                'level' => 'SMA',
                'status' => 'active',
            ],
        );
    }

    private function normalizeUsername(string $username): string
    {
        $username = strtolower(trim($username));
        $username = preg_replace('/\s+/', '', $username) ?? $username;
        $username = preg_replace('/[^a-z0-9._-]/', '', $username) ?? $username;

        return substr($username, 0, 30) ?: 'siswa';
    }

    /**
     * @param array<string, int> $occurrences
     */
    private function usernameForOccurrence(string $baseUsername, array &$occurrences): string
    {
        $occurrences[$baseUsername] = ($occurrences[$baseUsername] ?? 0) + 1;

        if ($occurrences[$baseUsername] === 1) {
            return $baseUsername;
        }

        $suffix = '-'.$occurrences[$baseUsername];

        return substr($baseUsername, 0, 30 - strlen($suffix)).$suffix;
    }

    /**
     * @param array<int, mixed> $row
     */
    private function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(fn ($value): bool => $this->clean((string) $value) !== '')->isEmpty();
    }

    private function clean(string $value): string
    {
        return trim($value);
    }
}
