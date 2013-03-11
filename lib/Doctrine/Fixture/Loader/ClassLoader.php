<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Fixture\Loader;

/**
 * ClassLoader loads a list of fixture classes.
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 */
class ClassLoader implements Loader
{
    /**
     * @var array<string>
     */
    private $classList;

    /**
     * Constructor.
     *
     * @param array $classList
     */
    public function __construct(array $classList = array())
    {
        $this->classList = $classList;
    }

    /**
     * Retrieve the list of classes.
     *
     * @return array
     */
    public function getClassList()
    {
        return $this->classList;
    }

    /**
     * Adds a new class to the list.
     *
     * @param string $class
     */
    public function addClass($class)
    {
        $this->classList[] = $class;
    }

    /**
     * Removes a class from the list.
     *
     * @param string $class
     */
    public function removeClass($class)
    {
        $this->classList = array_merge(array_diff($this->classList, array($class)));
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $classList   = array_unique($this->classList);
        $fixtureList = array();

        foreach ($classList as $class) {
            $reflectionClass = new \ReflectionClass($class);

            // Check if class is transient
            if ($this->isTransient($reflectionClass)) {
                continue;
            }

            $fixtureList[] = new $class();
        }

        return $fixtureList;
    }

    /**
     * Checks if class is transient.
     *
     * @param \ReflectionClass $reflectionClass
     *
     * @return boolean
     */
    private function isTransient(\ReflectionClass $reflectionClass)
    {
        if ($reflectionClass->isAbstract()) {
            return true;
        }

        return ( ! $reflectionClass->implementsInterface('Doctrine\Fixture\Fixture'));
    }
}