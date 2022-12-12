<?php declare(strict_types=1);

namespace App\Tests\FakeStore;

use App\FakeStore\UpdateProductCommand;
use App\FakeStore\Product;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UpdateProductCommandTest extends TestCase
{
    /**
     * Sample data from the API should be considered valid.
     * @dataProvider validSamples
     * @param Product $sample
     */
    public function testCreatedSuccessfully(Product $sample): void
    {
        $command = new UpdateProductCommand(
            $sample->id,
            $sample->title,
            $sample->price,
            $sample->description,
            $sample->category,
            $sample->image,
        );

        self::assertEquals($sample->id, $command->id);
        self::assertEquals($sample->title, $command->title);
        self::assertEquals($sample->price, $command->price);
        self::assertEquals($sample->description, $command->description);
        self::assertEquals($sample->category, $command->category);
        self::assertEquals($sample->image, $command->image);
    }

    /**
     * @dataProvider invalidSamples
     */
    public function testFailsAtValidation(array $input, string $expectedMessage): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($expectedMessage);

        new UpdateProductCommand(
            $input['id'],
            $input['title'],
            $input['price'],
            $input['description'],
            $input['category'],
            $input['image'],
        );
    }

    public function validSamples(): array
    {
        $products = SampleDataProvider::products();
        $samples = [];
        foreach ($products as $product) {
            $samples[sprintf('product id #%d', $product->id)] = [$product];
        }
        unset($products);

        return $samples;
    }

    public function invalidSamples(): array
    {
        $product = SampleDataProvider::products()[16-1];
        $data = [
            'id' => $product->id,
            'title' => $product->title,
            'price' => $product->price,
            'description' => $product->description,
            'category' => $product->category,
            'image' => $product->image,
        ];
        unset($product);

        // Key names describe the scenario.
        // Input reuses valid sample, but in each test a copy of it is modified accordingly.
        // Lastly, the exception message is included for comparison.
        return [
            'title is empty' => [
                'input' => (function () use ($data) {
                    $data['title'] = '';
                    return $data;
                })(),
                'expectedExceptionMessage' => 'title must not be empty',
            ],
            'title has only spaces' => [
                'input' => (function () use ($data) {
                    $data['title'] = '   ';
                    return $data;
                })(),
                'expectedExceptionMessage' => 'title must not be empty',
            ],
            'negative price' => [
                'input' => (function () use ($data) {
                    $data['price'] = -1.23;
                    return $data;
                })(),
                'expectedExceptionMessage' => 'price must not be negative',
            ],
            'no category' => [
                'input' => (function () use ($data) {
                    $data['category'] = '';
                    return $data;
                })(),
                'expectedExceptionMessage' => 'undefined category',
            ],
        ];
    }
}
