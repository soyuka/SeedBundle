# Symfony/Doctrine Seed Bundle

[![Build Status](https://travis-ci.org/soyuka/SeedBundle.svg?branch=master)](https://travis-ci.org/soyuka/SeedBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/bec4afda-4a87-4622-8aec-60ce66296617/mini.png)](https://insight.sensiolabs.com/projects/bec4afda-4a87-4622-8aec-60ce66296617)

/!\ Starting 01/01/18 I'll not actively maintain this project anymore. I'll merge PR's if any but I'm not using this anymore /!\

## Why

I needed something to load seed data that needed to stay in database. The only symfony bundle that could be used for this is the [DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle). Those are fixtures, and should be used for testing only. Data included with this are removed/purged when the command is launched. Here, I want the data to be persistent.
You can use it as a fixture bundle too by `unloading` data, but it is not it's main purpose.

## Configuration

```yaml
soyuka_seed:
  prefix: 'seed' #command prefix "seed:yourseedname"
  directory: 'Seeds' #default seed path: Bundle/Seeds
  separator: ':'
```

## Building a Seed

The `Seed` class is a `Command` and :

- Must extend `Soyuka\SeedBundle\Command\Seed`
- Must have a class name that ends by `Seed`
- Must call `setSeedName` in the configure method

```php
<?php

namespace AcmeBundle\ISOBundle\Seeds;

use Soyuka\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Parser;
use AcmeBundle\ISOBundle\Entity\Country;

class CountrySeed extends Seed
{

    protected function configure()
    {
        //The seed won't load if this is not set
        //The resulting command will be {prefix}:country
        $this
            ->setSeedName('country');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output){

        //Doctrine logging eats a lot of memory, this is a wrapper to disable logging
        $this->disableDoctrineLogging();

        //Access doctrine through $this->doctrine
        $countryRepository = $this->doctrine->getRepository('AcmeISOBundle:Country');

        $yaml = new Parser();

        //for example, using umpirsky/country-list (lazy yaml)
        $countries = $yaml->parse(file_get_contents('vendor/umpirsky/country-list/country/cldr/fr/country.yaml'));

        foreach ($countries as $id => $country) {

            if($countryRepository->findOneById($id)) {
                continue;
            }

            $e = new Country();

            $e->setId($id);
            $e->setName($country);

            //Doctrine manager is also available
            $this->manager->persist($e);

            $this->manager->flush();
        }

        $this->manager->clear();
    }

    public function getOrder() {
      return 0;
    }
}
```

## Loading a seed

The SeedBundle gives you two default commands and one for each seed you made. With the previous example, I'd have:

```
app/console seed:load #calls the load method of every seed
app/console seed:unload #calls the unload method of every seed
app/console seed:country
```

The global `seed:load` and `seed:unload` allow you to run multiple seeds in one command. You can of course skip seeds `app/console seed:load --skip Town` but also name the one you want `app/console seed:load Country`. For more informations, please use `app/console seed:load --help`.

## Seed order

Every seed has a `getOrder` method that is used to sort them. The default value is `0`.

## Licence

```
The MIT License (MIT)

Copyright (c) 2015 Antoine Bluchet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
