<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Country;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Currency;
use Doctrine\Common\Collections\ArrayCollection;

class CountrySyncCommand extends Command
{
    public function __construct( private HttpClientInterface $client, private EntityManagerInterface $entityManager) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('countries:sync');
        $this->setDescription('Synchronize the countries');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request('GET','https://restcountries.com/v3.1/all');
        if ($response->getStatusCode() !== 200)
           return COMMAND::FAILURE;

        $countries = $response->toArray();
        $this->clearTable('currency');
        $this->clearTable('country');
        foreach ($countries as $country) {
            $this->initialCountries(
                $country['name']['common'],
                $country['region'],
                $country['subregion']?? '',
                $country['demonyms']['eng']['m'] ?? '',
                $country['population']?? 0,
                $country['independent']?? true,
                $country['flags']['png']?? '',
                $country['currencies'] ?? [],
            );
        }

        
        return COMMAND::SUCCESS;
    }

    private function clearTable(string $tableName): void
    {
        $conn = $this->entityManager->getConnection();
        
        // Check if rows exist in the table
        $query = $conn->executeQuery("SELECT COUNT(*) FROM $tableName");
        $rowCount = $query->fetchOne();
        
        if ($rowCount > 0) {
            // Rows exist, proceed to delete
            $conn->executeStatement("DELETE FROM $tableName");
            echo "All rows have been deleted from $tableName.";
        } else {
            // No rows to delete
            echo "No rows found in $tableName.";
        }
    }

    private function initialCountries(
        string $name,
        string|null $region,
        string|null $subRegion,
        string|null $demonym,
        int $population,
        bool $independent,
        string|null $flag,
        array $currencies,
    ) {
        // dd($currencies); 

        $country = new Country();
        $country->setUuid(uniqid()); 
        $country->setName($name);
        $country->setRegion($region);
        $country->setSubRegion($subRegion);
        $country->setDemonym($demonym);
        $country->setPopulation($population);
        $country->setIndependent($independent);
        $country->setFlag($flag);
        $currencyCollection = new ArrayCollection();

        foreach ($currencies as $code => $currency) {
            $currentCurrency = $this->entityManager
                ->getRepository(Currency::class)
                ->findOneBy(['code' => $code]);
            if (empty($currentCurrency)) {
                $currencyName = $currency['name'];
                $currencySymbol = $currency['symbol'];

                // Set the currency
                $currentCurrency = new Currency();
                $currentCurrency->setName($currencyName);
                $currentCurrency->setSymbol($currencySymbol);
                $currentCurrency->setCountry($country);
                $currentCurrency->setCode($code);
                $this->entityManager->persist($currentCurrency);

            }
            $currencyCollection->add($currentCurrency);
        }

        $country->setCurrency($currencyCollection);
    
    
        // Persist the country object (if applicable)
        $this->entityManager->persist($country);
        $this->entityManager->flush();
    }
    
}