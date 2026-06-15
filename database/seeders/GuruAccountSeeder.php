<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class GuruAccountSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $guruRole = Role::findOrCreate('guru');
            $emailOwners = User::query()->whereNotNull('email')->pluck('username', 'email')->all();

            foreach ($this->rows() as $index => $row) {
                $username = $this->normalizeUsername($row['Username'] ?? 'guru'.($index + 1));
                $email = $this->uniqueEmail($row['Email'] ?? null, $username, $emailOwners);

                $user = User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $this->clean($row['Nama'] ?? $username),
                        'email' => $email,
                        'password' => $this->clean($row['Password'] ?? 'password'),
                        'status' => 'active',
                    ],
                );

                $user->syncRoles([$guruRole]);

                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    ['teacher_code' => 'GURU-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT), 'status' => 'active'],
                );
            }
        });
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function rows(): array
    {
        $file = new \SplFileObject(base_path('asssets/akun guru.csv'));
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $headers = [];
        $rows = [];

        foreach ($file as $line) {
            if (! is_array($line) || $this->isEmptyRow($line)) {
                continue;
            }

            if ($headers === []) {
                $headers = array_map(fn ($value): string => $this->clean((string) $value), $line);
                continue;
            }

            $rows[] = array_combine($headers, array_pad($line, count($headers), null));
        }

        return $rows;
    }

    private function normalizeUsername(string $username): string
    {
        $username = strtolower(trim($username));
        $username = preg_replace('/\s+/', '', $username) ?? $username;
        $username = preg_replace('/[^a-z0-9._-]/', '', $username) ?? $username;

        return substr($username, 0, 30) ?: 'guru';
    }

    /**
     * @param array<string, string> $emailOwners
     */
    private function uniqueEmail(?string $email, string $username, array &$emailOwners): ?string
    {
        $email = strtolower($this->clean((string) $email));

        if ($email === '') {
            return null;
        }

        if (isset($emailOwners[$email]) && $emailOwners[$email] !== $username) {
            return null;
        }

        $emailOwners[$email] = $username;

        return $email;
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
