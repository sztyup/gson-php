<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
namespace Tebru\Gson\Test\Unit\Internal;

use LogicException;
use PHPUnit\Framework\TestCase;
use Tebru\Gson\Internal\JsonEncodeWriter;

/**
 * Class JsonEncodeWriterTest
 *
 * @author Nate Brunette <n@tebru.net>
 * @covers \Tebru\Gson\Internal\JsonWriter
 * @covers \Tebru\Gson\Internal\JsonEncodeWriter
 * @covers \Tebru\Gson\Internal\JsonPath
 */
class JsonEncodeWriterTest extends TestCase
{
    public function testBeginArray(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();

        self::assertSame('[]', (string) $writer);
    }

    public function testBeginArrayDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->beginArray();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call beginArray() before name() during object serialization at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testEndArray(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->endArray();

        self::assertSame('[]', (string) $writer);
    }

    public function testEndArrayDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->endArray();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call endArray() if not serializing array at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testEndArrayFirst(): void
    {
        $writer = new JsonEncodeWriter();
        try {
            $writer->endArray();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call endArray() if not serializing array at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testNestedArrays(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->endArray();
        $writer->endArray();

        self::assertSame('[[1]]', (string) $writer);
    }

    public function testBeginObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();

        self::assertSame('{}', (string) $writer);
    }

    public function testBeginObjectDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->beginObject();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call beginObject() before name() during object serialization at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testEndObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->endObject();

        self::assertSame('{}', (string) $writer);
    }

    public function testEndObjectDuringArray(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        try {
            $writer->endObject();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call endObject() if not serializing object at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testEndObjectFirst(): void
    {
        $writer = new JsonEncodeWriter();
        try {
            $writer->endObject();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call endObject() if not serializing object at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testNestedObjects(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->beginObject();
        $writer->endObject();
        $writer->endObject();

        self::assertSame('{"foo":{}}', (string) $writer);
    }

    public function testName(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeInteger(1);
        $writer->endObject();

        self::assertSame('{"foo":1}', (string) $writer);
    }

    public function testNameTwice(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        try {
            $writer->name('foo');
        } catch (LogicException $exception) {
            self::assertSame('Cannot call name() at this point.  Either name() has already been called or object serialization has not been started at "$.foo"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testWriteInteger(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeInteger(1);

        self::assertSame('1', (string) $writer);
    }

    public function testWriteIntegerDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->writeInteger(1);
        } catch (LogicException $exception) {
            self::assertSame('Cannot call writeInteger() before name() during object serialization at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testWriteFloat(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeFloat(1.1);

        self::assertSame('1.1', (string) $writer);
    }

    public function testWriteFloatNonDecimal(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeFloat(1.0);

        self::assertSame('1', (string) $writer);
    }

    public function testWriteFloatDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->writeFloat(1);
        } catch (LogicException $exception) {
            self::assertSame('Cannot call writeFloat() before name() during object serialization at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testWriteString(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeString('foo');

        self::assertSame('"foo"', (string) $writer);
    }

    public function testWriteStringDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->writeString('foo');
        } catch (LogicException $exception) {
            self::assertSame('Cannot call writeString() before name() during object serialization at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testWriteBoolean(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeBoolean(true);

        self::assertSame('true', (string) $writer);
    }

    public function testWriteBooleanFalse(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeBoolean(false);

        self::assertSame('false', (string) $writer);
    }

    public function testWriteBooleanDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->writeBoolean(true);
        } catch (LogicException $exception) {
            self::assertSame('Cannot call writeBoolean() before name() during object serialization at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testWriteNull(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeNull();

        self::assertSame('null', (string) $writer);
    }

    public function testWriteNullDuringObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeNull();

        self::assertSame('{}', (string) $writer);
    }

    public function testWriteNullDuringObjectSerializeNulls(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->setSerializeNull(true);
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeNull();

        self::assertSame('{"foo":null}', (string) $writer);
    }

    public function testWriteNullDuringArraySerializeNulls(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->setSerializeNull(true);
        $writer->beginArray();
        $writer->writeNull();

        self::assertSame('[null]', (string) $writer);
    }

    public function testWriteNullDuringBeginObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        try {
            $writer->writeNull();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call writeNull() before name() during object serialization at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testWriteTwoScalars(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeString('foo');
        try {
            $writer->writeString('bar');
        } catch (LogicException $exception) {
            self::assertSame('Attempting to write two different types at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testWriteTwice(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->endObject();
        try {
            $writer->writeString('bar');
        } catch (LogicException $exception) {
            self::assertSame('Attempting to write two different types at "$"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testPathBeginObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        self::assertSame('$', $writer->getPath());
    }

    public function testPathName(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        self::assertSame('$.foo', $writer->getPath());
    }

    public function testPathObjectValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeInteger(1);
        self::assertSame('$.foo', $writer->getPath());
    }

    public function testPathObjectSecondValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeInteger(1);
        $writer->name('bar');
        $writer->writeBoolean(true);
        self::assertSame('$.bar', $writer->getPath());
    }

    public function testPathObjectSerializeNull(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->setSerializeNull(true);
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeInteger(1);
        $writer->name('bar');
        $writer->writeNull();
        self::assertSame('$.bar', $writer->getPath());
    }

    public function testPathObjectNotSerializeNull(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->setSerializeNull(false);
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeInteger(1);
        $writer->name('bar');
        $writer->writeNull();

        // even though we're not serializing nulls, we need to keep track of the place
        // this is different than arrays where we pretend the value doesn't exist
        self::assertSame('$.bar', $writer->getPath());
    }

    public function testPathObjectInObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->beginObject();
        self::assertSame('$.foo', $writer->getPath());
    }

    public function testPathObjectInObjectValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->beginObject();
        $writer->name('bar');
        self::assertSame('$.foo.bar', $writer->getPath());
    }

    public function testPathArrayInObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->beginArray();
        self::assertSame('$.foo', $writer->getPath());
    }

    public function testPathArrayInObjectValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->beginArray();
        $writer->writeInteger(1);
        self::assertSame('$.foo[0]', $writer->getPath());
    }

    public function testPathEndObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('foo');
        $writer->writeInteger(1);
        $writer->endObject();
        self::assertSame('$', $writer->getPath());
    }

    public function testPathBeginArray(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        self::assertSame('$', $writer->getPath());
    }

    public function testPathArrayValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->writeString('foo');
        self::assertSame('$[0]', $writer->getPath());
    }

    public function testPathArraySecondValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->writeString('foo');
        $writer->writeString('bar');
        self::assertSame('$[1]', $writer->getPath());
    }

    public function testPathArraySerializeNull(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->setSerializeNull(true);
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->writeNull();
        self::assertSame('$[1]', $writer->getPath());
    }

    public function testPathArrayNotSerializeNull(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->setSerializeNull(false);
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->writeNull();
        self::assertSame('$[0]', $writer->getPath());
    }

    public function testPathArrayInArray(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->beginArray();
        self::assertSame('$[0]', $writer->getPath());
    }

    public function testPathArrayInArrayValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->beginArray();
        $writer->writeInteger(1);
        self::assertSame('$[0][0]', $writer->getPath());
    }

    public function testPathObjectInArray(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->beginObject();
        self::assertSame('$[0]', $writer->getPath());
    }

    public function testPathObjectInArrayValue(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->beginObject();
        $writer->name('foo');
        self::assertSame('$[0].foo', $writer->getPath());
    }

    public function testPathEndArray(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginArray();
        $writer->writeString('foo');
        $writer->writeString('bar');
        $writer->endArray();
        self::assertSame('$', $writer->getPath());
    }

    public function testPathInteger(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeInteger(1);
        self::assertSame('$', $writer->getPath());
    }

    public function testPathString(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeString('foo');
        self::assertSame('$', $writer->getPath());
    }

    public function testPathFloat(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeFloat(1.5);
        self::assertSame('$', $writer->getPath());
    }

    public function testPathBoolean(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->writeBoolean(false);
        self::assertSame('$', $writer->getPath());
    }

    public function testComplexObject(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('name');
        $writer->beginObject();
        $writer->name('nested');
        $writer->writeInteger(2);
        $writer->name('array');
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->writeInteger(2);
        $writer->endArray();
        $writer->endObject();
        $writer->name('second');
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->beginObject();
        $writer->name('nested2');
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->writeInteger(2);

        self::assertSame('$.second[1].nested2[1]', $writer->getPath());
    }

    public function testComplexObjectFail(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->beginObject();
        $writer->name('name');
        $writer->beginObject();
        $writer->name('nested');
        $writer->writeInteger(2);
        $writer->name('array');
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->writeInteger(2);
        $writer->endArray();
        $writer->endObject();
        $writer->name('second');
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->beginObject();
        $writer->name('nested2');
        $writer->beginArray();
        $writer->writeInteger(1);
        $writer->writeInteger(2);
        $writer->endArray();

        try {
            $writer->endArray();
        } catch (LogicException $exception) {
            self::assertSame('Cannot call endArray() if not serializing array at "$.second[1].nested2"', $exception->getMessage());
            return;
        }
        self::fail('Failed to throw exception');
    }

    public function testSerializeNullDefault(): void
    {
        $writer = new JsonEncodeWriter();

        self::assertFalse($writer->isSerializeNull());
    }

    public function testIsSerializeNull(): void
    {
        $writer = new JsonEncodeWriter();
        $writer->setSerializeNull(true);

        self::assertTrue($writer->isSerializeNull());
    }
}
