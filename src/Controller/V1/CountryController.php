<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Currency;

#[Route('/countries')]
class CountryController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;


    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/{countryUuid}', methods: ['GET'])]
    public function getCountry(string $countryUuid): Response
    {
        $country = $this->entityManager->getRepository(Country::class)->findOneBy(['uuid' => $countryUuid]);

        if (!$country) {
            return $this->json(['error' => 'Country not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($country, 'json', ['groups' => ['country_details']]);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/', methods: ['GET'])]
    public function getCountries(): Response
    {
        $countries = $this->entityManager->getRepository(Country::class)->findAll();
        $data = $this->serializer->serialize($countries, 'json', ['groups' => ['country_details']]);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/', methods: ['POST'])]
    public function addCountry(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $country = new Country();
        $country->setName($data['name']);
        $country->setRegion($data['region'] ?? null);
        $country->setSubRegion($data['subRegion'] ?? null);
        $country->setDemonym($data['demonym'] ?? null);
        $country->setPopulation($data['population']);
        $country->setIndependent($data['independent']);
        $country->setFlag($data['flag'] ?? null);
        $country->setUuid(uniqid());

        $currencyCollection = new ArrayCollection();

        foreach ($data['currencies'] as $currency) {
            $currentCurrency = $this->entityManager
                ->getRepository(Currency::class)
                ->findOneBy(['code' => $currency['code']]);
            if (empty($currentCurrency)) {
                $currencyName = $currency['name'];
                $currencySymbol = $currency['symbol'];

                // Set the currency
                $currentCurrency = new Currency();
                $currentCurrency->setName($currencyName);
                $currentCurrency->setSymbol($currencySymbol);
                $currentCurrency->setCountry($country);
                $currentCurrency->setCode($currency['code']);
                $this->entityManager->persist($currentCurrency);

            }
            $currencyCollection->add($currentCurrency);
        }

        $country->setCurrency($currencyCollection);

        $this->entityManager->persist($country);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($country, 'json',  ['groups' => ['country_details']]);
        return new Response($data, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    #[Route('/{countryUuid}', methods: ['PATCH'])]
    public function updateCountry(string $countryUuid, Request $request): Response
    {
        $country = $this->entityManager->getRepository(Country::class)->findOneBy(['uuid' => $countryUuid]);

        if (!$country) {
            return $this->json(['error' => 'Country not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $country->setName($data['name']);
        if (isset($data['region'])) $country->setRegion($data['region']);
        if (isset($data['subRegion'])) $country->setSubRegion($data['subRegion']);
        if (isset($data['demonym'])) $country->setDemonym($data['demonym']);
        if (isset($data['population'])) $country->setPopulation($data['population']);
        if (isset($data['independent'])) $country->setIndependent($data['independent']);
        if (isset($data['flag'])) $country->setFlag($data['flag']);


        // Update the currencies array
        if (isset($data['currencies'])) {
            $existingCurrencies = $country->getCurrency();
            $currencyRepo = $this->entityManager->getRepository(Currency::class);

            // Loop through new currencies and update/add them
            foreach ($data['currencies'] as $currencyData) {
                $currency = $currencyRepo->findOneBy(['code' => $currencyData['code']]);

                if (!$currency) {
                    // If currency doesn't exist, create a new one
                    $currency = new Currency();
                    $currency->setCode($currencyData['code']);
                    $currency->setName($currencyData['name']);
                    $currency->setSymbol($currencyData['symbol']);
                    $currency->setCountry($country);

                    $this->entityManager->persist($currency);
                } else {
                    // If the currency exists, update its details
                    $currency->setName($currencyData['name']);
                    $currency->setSymbol($currencyData['symbol']);
                    $currency->setCountry($country);
                }

                // Add the currency to the country's collection if it's not already there
                if (!$existingCurrencies->contains($currency)) {
                    $country->getCurrency()->add($currency);
                }
            }

            // Remove currencies that are no longer present in the new data
            foreach ($existingCurrencies as $existingCurrency) {
                if (!in_array($existingCurrency->getCode(), array_column($data['currencies'], 'code'))) {
                    $country->getCurrency()->removeElement($existingCurrency);
                    $this->entityManager->remove($existingCurrency); // Optionally delete the currency if it's removed
                }
            }
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($country, 'json',  ['groups' => ['country_details']]);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }


    #[Route('/{countryUuid}', methods: ['DELETE'])]
    public function deleteCountry(string $countryUuid): Response
    {
        $country = $this->entityManager->getRepository(Country::class)->findOneBy(['uuid' => $countryUuid]);

        if (!$country) {
            return $this->json(['error' => 'Country not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($country);
        $this->entityManager->flush();

        return new Response('Successfully deleted', Response::HTTP_NO_CONTENT);
    }
}
